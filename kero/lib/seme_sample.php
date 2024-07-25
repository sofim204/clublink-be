<?php
Class Seme_Sample {
  var $product_file;
  public function __construct(){
    $this->product_file = SENELIB.DIRECTORY_SEPARATOR.'seme_sample'.DIRECTORY_SEPARATOR.'product.csv';
  }
  public function getProductName(){
    if(!file_exists($this->product_file)){
      trigger_error("SEME_SAMPLE cannot get Product file");
    }
    $file = fopen($this->product_file,"r");
    $products = fgetcsv($file);
    fclose($file);
    unset($file);
    $max = count($products);
    $rand = rand(0,($max-1));
    $name = '';
    if(isset($products[$rand])) $name = $products[$rand];
    return $name;
  }
}
