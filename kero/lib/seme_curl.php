<?php
class Seme_Curl {
  var $info;
  var $ua = 'Mozilla/5.0 (Linux; U; Android 2.3.3; de-de; HTC Desire Build/GRI40) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1';
	public function __construct(){
    $this->info = new stdClass();
    $this->info->header = new stdClass();
    $this->info->body = new stdClass();
  }
  private function __convertObject($a,$level=0){
    if($level > 5) {
      throw new OverflowException(sprintf('%s stack overflow: %d exceeds max recursion level', __METHOD__, $level));
    }
    $o = new stdClass();
    foreach($a as $key => $value) {
        if(is_array($value)) { // convert value recursively
          $value = $this->__convertObject($value, $level+1);
        }
        $o->{$key} = $value;
    }
    return $o;
  }
  public function init(){
    $this->info = new stdClass();
    $this->info->header = new stdClass();
    $this->info->body = new stdClass();
    return $this;
  }
	public function get($url,$is_follow=0){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);
    if(!empty($is_follow)) curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if(!isset($_SERVER['argv'])) curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$this->info->body = curl_exec($ch);
    $this->info->header = $this->__convertObject(curl_getinfo($ch));
		curl_close($ch);
		return $this->info;
	}
	public function post($url,$postdata=array(),$is_follow=0){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($is_follow)) curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if(!isset($_SERVER['argv'])) curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$this->info->body = curl_exec($ch);
    $this->info->header = $this->__convertObject(curl_getinfo($ch));
		curl_close($ch);
		return $this->info;
	}
	public function basicAuthJson($url,$username,$password){
		$additionalHeaders = '';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $additionalHeaders));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
		//curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}
	public function basicAuthXml($url,$username,$password,$postdata=array()){
		$additionalHeaders = '';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', $additionalHeaders));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
		//curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}
}
