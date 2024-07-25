<?php
class Alamat extends JI_Controller{

    public $is_log = 1; 

	public function __construct(){
  	parent::__construct();

    $this->lib("seme_log");
		//$this->setTheme('frontx');
		$this->load("api_mobile/a_negara_model","anm");
		$this->load("api_mobile/b_lokasi_model","blokm");
		$this->load("api_mobile/b_kodepos_model","bkpm");
		
	}

	private function __passClear($str){
		return preg_replace('/^[a-z0-9\040\.\-]+$/i', '', $str);
	}

	public function index(){
		$dt = $this->__init();
		$data = array();

		$apikey = $this->input->get('apikey');

		//by Donny Dennison - 18 august 2020 11:25
		// fix some missing apikey didnt return status 400
		// $this->status = 199;
		$this->status = 400;

		$this->message = 'Missing or invalid apikey';
		$c = $this->apikey_check($apikey);

		$data['sliders'] = array();
		if($c){
			$this->status = 200;
			$this->message = 'Success';
			$data['sliders'] = $this->dsm->get('mobile',1);
			foreach($data['sliders'] as &$slider){
				if($slider->utype == 'internal'){
					$slider->image = base_url($slider->image);
				}
			}
		}
		// $this->__json_out($data);
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "alamat");
		die();
	}
	public function negara(){
    //init
		$dt = $this->__init();

    //default result format
		$data = array();
		$data['negara'] = array();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data);
      die();
    }

    //default message
		$this->status = 400;
		$this->message = 'Missing or invalid apikey';

    //check apikey
		$apikey = $this->input->get('apikey');
		$ca = $this->apikey_check($apikey);
    if(empty($ca)){

    	//by Donny Dennison - 18 august 2020 11:25
			// fix some missing apikey didnt return status 400
    	// $this->status = 199;
    	$this->status = 400;

  		$this->message = 'Missing or invalid apikey';
      $this->__json_out($data);
      die();
    }
		//fetch data
		$data['negara'] = $this->anm->get($nation_code);

		//default response
		$this->status = 200;
		$this->message = "Success";
		//render
		$this->__json_out($data);
	}

	public function lokasi(){
    //init
		$dt = $this->__init();

    //default result format
		$data = array();
		$data['lokasi'] = array();

    //check nation_code
		$nation_code = $this->input->get('nation_code');
		$nation_code = $this->nation_check($nation_code);
    if(empty($nation_code)){
      $this->status = 101;
  		$this->message = 'Missing or invalid nation_code';
      $this->__json_out($data);
      die();
    }

    //default message
		$this->status = 400;
		$this->message = 'Missing or invalid apikey';

    //check apikey
		$apikey = $this->input->get('apikey');
		$ca = $this->apikey_check($apikey);
    if(empty($ca)){

    	//by Donny Dennison - 18 august 2020 11:25
			// fix some missing apikey didnt return status 400
      // $this->status = 199;
      $this->status = 400;

  		$this->message = 'Missing or invalid apikey';
      $this->__json_out($data);
      die();
    }

		$kecamatan = $this->input->get("kecamatan");
		if(empty($kecamatan)) $kecamatan = '';

		$kelurahan = $this->input->get("kelurahan");
		if(empty($kelurahan)) $kelurahan = '';

		$keyword = $this->input->get("keyword");
		if(empty($keyword)) $keyword = '';
		$keyword = filter_var(strip_tags($keyword),FILTER_SANITIZE_SPECIAL_CHARS);
		$keyword = substr($keyword,0,32);

		if(strlen($keyword)<=1){
			$this->status = 409;
			$this->message = 'Keyword too short';
			$this->__json_out($data);
			die();
		}

		//get lokasi from db
		$data['lokasi'] = $this->blokm->search($nation_code, $keyword);

		//default response
		$this->status = 200;
		$this->message = "Success";
		//render
		$this->__json_out($data);
	}

	//by Donny Dennison - 24 juli 2020 10:37
	// get address detail from vendor SG Locate
	public function __callVendorSGLocateByPostCode($postCode)
    {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        curl_setopt($ch, CURLOPT_URL, $this->sglocate_api_host.'searchwithpostcode.aspx');
        $headers = array();
        // $headers[] = 'Content-Type: Text/xml';
        // $headers[] = 'Accept: Text/xml';
        $postdata = array(
          'APIKey' => $this->sglocate_api_key,
          'APISecret' => $this->sglocate_api_secret,
          'Postcode' => $postCode,

        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        if($this->is_log){
          $this->seme_log->write("api_mobile", 'API_Mobile/Alamat::__callVendorSGLocateByPostCode:: -- cUrlHeader: '.json_encode($headers));
          $this->seme_log->write("api_mobile", 'API_Mobile/Alamat::__callVendorSGLocateByPostCode:: -- cUrlPOST: '.json_encode($postdata));
        }
        return $result;
    }

	//by Donny Dennison - 24 juli 2020 10:37
	// get address detail from vendor SG Locate
	public function __callVendorSGLocateByBlockNumberAndStreetName($block, $streetName)
    {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        curl_setopt($ch, CURLOPT_URL, $this->sglocate_api_host.'searchwithblocknumberandstreetname.aspx');
        $headers = array();
        // $headers[] = 'Content-Type: Text/xml';
        // $headers[] = 'Accept: Text/xml';
        $postdata = array(
          'APIKey' => $this->sglocate_api_key,
          'APISecret' => $this->sglocate_api_secret,
          'Block' => $block,
          'StreetName' => $streetName

        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 0;
            //echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        if($this->is_log){
          $this->seme_log->write("api_mobile", 'API_Mobile/Alamat::__callVendorSGLocateByBlockNumberAndStreetName:: -- cUrlHeader: '.json_encode($headers));
          $this->seme_log->write("api_mobile", 'API_Mobile/Alamat::__callVendorSGLocateByBlockNumberAndStreetName:: -- cUrlPOST: '.json_encode($postdata));
        }
        return $result;
    }


	// //by Donny Dennison - 24 juli 2020 10:37
	// // get address detail from vendor SG Locate
	// public function getAddressFromVendor(){
	// 	$dt = $this->__init();
	// 	$data = array();

	// 	$apikey = $this->input->get('apikey');

	// 	//by Donny Dennison - 18 august 2020 11:25
	// 	// fix some missing apikey didnt return status 400
	// 	// $this->status = 199;
	// 	$this->status = 400;

	// 	$this->message = 'Missing or invalid apikey';
	// 	$c = $this->apikey_check($apikey);

	// 	//check nation_code
	// 	$nation_code = $this->input->get('nation_code');
	// 	$nation_code = $this->nation_check($nation_code);
	//     if(empty($nation_code)){
	//      	$this->status = 101;
	//   		$this->message = 'Missing or invalid nation_code';
	//      	$this->__json_out($data);
	//      	die();
	//     }
		
	// 	if($c){

	// 		$address = trim($this->input->post("address"));

	// 		//validation
	// 		if(empty($address)){
	// 			$this->status = 301;
	// 			// $this->message = 'Please provide either block or street name';
	// 			$this->message = 'Please enter zip code or street name(building no)';
	// 			$this->__json_out($data);
	// 			die();
	// 		}

	// 		if(is_numeric($address)){

	// 			if(strlen($address) < 6){

	// 				$this->status = 302;
	// 				// $this->message = 'You must provide at least 6 numbers for the zipcode.';
	// 				$this->message = 'Zip code is wrong';
	// 				$this->__json_out($data);
	// 				die();

	// 			}

	// 		}else{

	// 			$explode = explode(' ', $address);

	// 			$returnErrorResponse = 0;

	// 			if(count($explode) < 1){

	// 				$returnErrorResponse = 1;

	// 			}

	// 			$combineArrayBecomeString = '';
	// 			foreach ($explode as $key => $value) {
				 	
	// 				if($key == 0){

	// 					if(strlen($value) < 1){
						
	// 						$returnErrorResponse = 1;
	// 						break;

	// 					}
						
	// 				}else{

	// 					$combineArrayBecomeString .= $value;

	// 					if($key+1 < count($explode)){

	// 						$combineArrayBecomeString .= " ";

	// 					}

	// 				}

	// 			}
				
	// 			if(strlen(trim($combineArrayBecomeString)) < 3){

	// 				$returnErrorResponse = 1;

	// 			}
			

	// 			if($returnErrorResponse == 1){

	// 				$this->status = 303;
	// 				// $this->message = 'You must provide at least 1 character of the block or 3 characters for street name.';
	// 				$this->message = 'Zip code is wrong';
	// 				$this->__json_out($data);
	// 				die();

	// 			}

	// 		}

	// 		if(is_numeric($address) && strlen($address) > 5){

	// 			$responseFromVendor = $this->__callVendorSGLocateByPostCode($address);

	// 		}else{

	// 			$explode = explode(' ', $address);

	// 			$count = count($explode);

	// 			$block = '';
	// 			$streetName = '';

	// 			foreach ($explode as $key => $value) {

	// 				if($key == 0){

	// 					$block = $value;

	// 				}else{

	// 					$streetName .= $value;

	// 					if($key+1 < count($explode)){

	// 						$streetName .= " ";

	// 					}

	// 				}

	// 			}

	// 			$responseFromVendor = $this->__callVendorSGLocateByBlockNumberAndStreetName($block, $streetName);

	// 		}

	// 		$responseFromVendor = json_decode($responseFromVendor);

	// 		if($responseFromVendor->ErrorCode == 1 && $responseFromVendor->IsSuccess == true){

	// 			if($responseFromVendor->Postcodes[0]->Latitude != 0){
	// 				$this->status = 200;
	// 				$this->message = 'Success';
	// 				$data = $responseFromVendor->Postcodes;
	// 			}else{
	// 				$this->status = 304;
	// 				$this->message = 'Inputted zip code or street name(building no) has empty latitude and longitude';
	// 			}

	// 		}else{

	// 			$this->status = 304;

	// 			if($responseFromVendor->ErrorCode == -15){
	// 				$this->message = 'There is no address with this zip code';
	// 			}else{
	// 				$this->message = $responseFromVendor->ErrorDetails;
	// 			}

	// 		}
				
	// 	}

	// 	$this->__json_out($data);
	// }

}
