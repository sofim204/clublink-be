<?php
class Seme_Log {
	var $directory = '';
	var $filename = 'seme.log';
	var $path = '';

	public function __construct(){
		$this->directory = SENEROOT;
		$this->path = $this->directory.DIRECTORY_SEPARATOR.$this->filename;
		if(!file_exists($this->path)) touch($this->path);
		if(!is_writable($this->path)){
			$this->directory = SENECACHE;
			$this->path = $this->directory.DIRECTORY_SEPARATOR.$this->filename;
			if(is_writable($this->path)) touch($this->path);
		}
	}

	public function changeFilename($filename){
		$this->filename = $filename;
		$this->directory = SENEROOT;
		$this->path = $this->directory.DIRECTORY_SEPARATOR.$this->filename;
		if(!file_exists($this->path)) touch($this->path);
		if(!is_writable($this->path)){
			$this->directory = SENECACHE;
			$this->path = $this->directory.DIRECTORY_SEPARATOR.$this->filename;
			if(is_writable($this->path)) touch($this->path);
		}
	}

	//START by Donny Dennison - 26 december 2022 15:29
	//improve seme log
	// public function write($str){
	// 	$f = fopen($this->path,'a+');
	// 	fwrite($f,date("Y-m-d H:i:s").' - ');
	// 	fwrite($f,$str.PHP_EOL);
	// 	fclose($f);
	// }
	public function write($folder, $str){
	    $targetdir = "storage/chat/custom_log";
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

	    $targetdir = $targetdir.DIRECTORY_SEPARATOR.$folder;
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

		$f = fopen(SENEROOT.$targetdir.DIRECTORY_SEPARATOR.date("Y-m-d").".log",'a+');
		fwrite($f,date("Y-m-d H:i:s").' - ');
		fwrite($f,$str.PHP_EOL);
		fclose($f);
	}
	//END by Donny Dennison - 26 december 2022 15:29
	//improve seme log
  
	public function getPath(){
		return $this->path;
	}

}
