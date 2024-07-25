<?php
Class Runner_Controller extends SENE_Controller {
  var $ite=1;
  var $is_cli = 0;
  var $nation_code = '65';
  var $apikey = 'kmz373ac';
  var $apisess = '65KMZDS';
  var $url = '';
  var $url_aft = '';
  var $url_page = '';
  var $sort_col = 'id';
  var $sort_dir = 'asc';
  var $page = 1;
  var $page_size = 10;
  var $time_start = 0;
  var $mem_start = 0;
  public function __construct(){
    parent::__construct();
    if(isset($_SERVER['argv'])) $this->is_cli = 1;
    $this->lib("seme_curl");
    $this->url = base_url('api_mobile/');
    $this->url_aft .= '/';
    $this->url_aft .= '?nation_code='.$this->__encURICom($this->nation_code);
    $this->url_aft .= '&apikey='.$this->__encURICom($this->apikey);
    $this->url_aft .= '&apisess='.$this->__encURICom($this->apisess);

    $this->url_page  = $this->url_aft;
    $this->url_page .= '&page='.$this->page;
    $this->url_page .= '&page_size='.$this->page_size;
    $this->url_page .= '&sort_col='.$this->sort_col;
    $this->url_page .= '&sort_dir='.$this->sort_dir;
    if(!isset($_SERVER["REQUEST_TIME_FLOAT"])){
      $this->time_start = microtime(true);
    }else{
      $this->time_start = $_SERVER["REQUEST_TIME_FLOAT"];
    }
    $this->mem_start = memory_get_usage();
  }
  protected function __setApiSess($apisess){
    $this->apisess = $apisess;
    $this->__resetURL();
  }
  protected function __setSortCol($sort_col){
    $this->sort_col = $sort_col;
    $this->__resetPage();
  }
  protected function __setSortDir($sort_dir){
    $this->sort_dir = $sort_dir;
    $this->__resetPage();
  }
  protected function __toKB($f){
    return round(($f/1024/1024),5);
  }
  protected function __toMS($f){
    return round($f,5);
  }

  protected function __encURICom($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
  }
  protected function __baseUrl($url){
    $this->url = $url;
    $this->__resetURL();
    $this->__resetPage();
  }
  protected function __resetURL(){
    $this->url_aft = '/';
    $this->url_aft .= '?nation_code='.$this->__encURICom($this->nation_code);
    $this->url_aft .= '&apikey='.$this->__encURICom($this->apikey);
    $this->url_aft .= '&apisess='.$this->__encURICom($this->apisess);
  }
  protected function __resetPage(){
    $this->page = 1;
    $this->url_page  = $this->url_aft;
    $this->url_page .= '&page='.$this->page;
    $this->url_page .= '&page_size='.$this->page_size;
    $this->url_page .= '&sort_col='.$this->sort_col;
    $this->url_page .= '&sort_dir='.$this->sort_dir;
  }
  protected function __pageNext(){
    $this->page = $this->page+1;
    $this->url_page  = $this->url_aft;
    $this->url_page .= '&page='.$this->page;
    $this->url_page .= '&page_size='.$this->page_size;
    $this->url_page .= '&sort_col='.$this->sort_col;
    $this->url_page .= '&sort_dir='.$this->sort_dir;
  }
  protected function __pageBefore(){
    $this->page = $this->page-1;
    $this->url_page  = $this->url_aft;
    $this->url_page .= '&page='.$this->page;
    $this->url_page .= '&page_size='.$this->page_size;
    $this->url_page .= '&sort_col='.$this->sort_col;
    $this->url_page .= '&sort_dir='.$this->sort_dir;
  }
  protected function __vo($e){
    if($this->is_cli){
      print('========================================================'.PHP_EOL);
      print($e.PHP_EOL);
      print('base_url:'.$this->url.PHP_EOL);
      print('Runner memory: '.$this->__toKB(memory_get_usage(true)).' MBytes'.PHP_EOL);
      print('--------------------------------------------------------'.PHP_EOL);
    }else{
      echo '<!DOCTYPE html>'."\n";
      echo '<html><head><title>'.$e.'</title>';
      echo '<style>';
      echo '* { font-family: arial, verdana;}';
      echo 'body {margin: 1em 0; padding: 0 10%; background-color: #fafafa;}';
      echo '.container{ padding: 1em; background-color: #ffffff; border: 1px solid #ededed;}';
      echo '.header{ padding: 1em; background-color: #dadada;}';
      echo 'h1{margin: 0.5em 0;}';
      echo 'h3,h4 {margin:0;} p {line-height: 1; margin: 0.5em;} .result {font-weight: bold;}  pre { background-color: #fafafa; margin: 0.5em 0; padding: 0 0.5em; }';
      echo 'pre{background-color: #0a0a0a; color: white; padding:1em;font-family: courier;font-size:14px;white-space:pre-wrap;word-wrap:break-word; margin-bottom: 1em;}';
      echo '.pre-wrap {padding: 0 1em}';
      echo 'pre.full{height: auto;} pre.limited{height: 50px;}';
      echo 'p.italic{font-family: "Times New Roman", times; font-style: italic; margin-bottom: 1.5em;}';
      echo 'footer {font-family: "Times New Roman", times; font-style: italic;margin: 1em 0; text-align: center;}';
      echo '.result.passed {color: green;} .result.warning {color: khaki;} .result.error {color: red;}';
      echo '.seme {font-style: normal; font-family: arial, verdana;}';
      echo '</style>';
      echo '</head>';
      echo '<body>';
      echo '<div class="header">';
      echo '<h1>'.$e.'</h1>';
      echo '<p class="">base_url: '.$this->url.'</p>';
      echo '<p class="">Allocated Memory: '.$this->__toKB(memory_get_usage(true)).' KBytes</p>';
      echo '</div>';
      echo '<div class="container">';
    }
  }
  protected function __vu($title,$url){
    if($this->is_cli){
      print($this->ite.'. '.$title.PHP_EOL);
      print('Calling: '.$url.PHP_EOL);
    }else{
      echo ('<h4>'.$this->ite.'. '.$title.'</h4>');
      echo ('<p>Calling: <a href="'.$url.'" target="_blank">Endpoint</a></p>'.PHP_EOL);
    }
    $this->ite++;
  }
  protected function __vr($e){
    if($this->is_cli){
      print('Result: '.$e.PHP_EOL);
    }else{
      echo('<p>Result: <span class="result">'.$e.'</span></p>');
    }
  }
  protected function __vrp($e){
    if($this->is_cli){
      print('Result: '.$e.PHP_EOL);
    }else{
      echo('<p>Result: <span class="result passed">'.$e.'</span></p>');
    }
  }
  protected function __vrr($e){
    if($this->is_cli){
      print('Result: '.$e.PHP_EOL);
    }else{
      echo('<p>Result: <span class="result error">'.$e.'</span></p>');
    }
  }
  protected function __vrh($e){
    if($this->is_cli){
      print('Result: '.$e.PHP_EOL);
    }else{
      echo('<p>Result: <span class="result warning">'.$e.'</span></p>');
    }
  }
  protected function __vs($e){
    if($this->is_cli){
      print($e.PHP_EOL);
    }else{
      echo('<p>'.$e.'</p>');
    }
  }
  protected function __vd($e){
    if($this->is_cli){
    }else{
      $e = substr($e,0,300);
      echo '<div class="pre-wrap"><pre class="limited">'.$e.'</pre></div>';
    }
  }
  protected function __vdf($e){
    if($this->is_cli){
    }else{
      echo '<div class="pre-wrap"><pre class="">'.$e.'</pre></div>';
    }
  }
  protected function __vc($e){
    if($this->is_cli){
    }else{
      echo '<div class="pre-wrap"><pre class="">'.$e.'</pre></div>';
    }
  }
  protected function __vb($sz=""){
    $time_end = microtime(true) - $this->time_start;
    if(strlen($sz)<=0) $sz = $this->__toKB(memory_get_usage()).' Kbytes';
    if($this->is_cli){
      print('--------------------------------------------------------'.PHP_EOL);
      print('|   Executed in: '.$this->__toMS($time_end).' seconds    |'.PHP_EOL);
      print('|   Memory Usage: '.$sz.'                |'.PHP_EOL);
      print('--------------------------------------------------------'.PHP_EOL);
    }else{
      echo '<p class="italic">Executed in: '.$this->__toMS($time_end).' seconds | Memory Usage: '.$sz.'</p>';
    }
  }
  protected function __ve(){
    $time_end = microtime(true) - $this->time_start;
    $mem_end = memory_get_usage();
    if($this->is_cli){
      print('--------------------------------------------------------'.PHP_EOL);
      print('|   Finished    |'.PHP_EOL);
      print('|   Total execution time = '.$this->__toMS($time_end).' seconds    |'.PHP_EOL);
      print('|   Final Memory: '.$this->__toKB($mem_end).' MBytes               |'.PHP_EOL);
      print('========================================================'.PHP_EOL);
    }else{
      echo '</div>';
      echo '<footer>Total execution time: '.$this->__toMS($time_end).' seconds | Runner Memory: '.$this->__toKB($mem_end).' MBytes<br /><span class="seme">Seme Framework v'.SENE_VERSION.'</span></footer>';
      echo '</body></html>';
    }
  }

  public function index(){

  }

}
