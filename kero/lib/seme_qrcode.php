<?php
Class Seme_QRCode {
  public $root = '';
  public $media = 'media/pelanggan/';
  public function __construct(){
    $this->root = getcwd();
    $cwd = dirname(__FILE__);
    require_once($cwd."/phpqrcode/qrlib.php");
  }
  public function media($media){
    $this->media = $media;
  }
  public function root($root){
    $this->root = $root;
  }
  public function write($str,$filename="",$ext="png"){
    if(strlen($filename)==0) $filename = date("ymdHis");
    if(is_file($this->root.$this->media.$filename.".png") && file_exists($this->root.$this->media.$filename.".png")){
      unlink($this->root.$this->media.$filename.".png");
    }
    QRcode::png($str,$this->root.$this->media.$filename.".png","Q","3","1");
    return $this->media.$filename.".png";
  }
}