<?php
class Pelanggan extends JI_Controller{
	var $is_email = 1;

	public function __construct(){
    parent::__construct();
		//$this->setTheme('frontx');
		$this->load("api_admin/c_produk_model",'cpm');
		$this->load("api_admin/d_order_model",'dom');
		$this->load("api_admin/d_order_detail_model",'dodm');
		$this->current_parent = 'laporan';
		$this->current_page = 'laporan_penjualan';
	}
	public function index(){
		$d = $this->__init();
		$data = array();
		if(!$this->admin_login){
			$this->status = 400;
			$this->message = 'Harus login';
			header("HTTP/1.0 400 Harus login");
			$this->__json_out($data);
			die();
		}
		$mindate = $this->input->request('mindate');
		$maxdate = $this->input->request('maxdate');
		if(!empty($mindate) && empty($maxdate)) $maxdate = $mindate;
		if(!empty($maxdate) && empty($mindate)) $mindate = $maxdate;
		if(!empty($maxdate) && !empty($mindate)){
			$mindate = date("Y-m-d",strtotime($mindate));
			$maxdate = date("Y-m-d",strtotime($maxdate));
			$penjualan = $this->dom->laporanPenjualanSelesai($mindate,$maxdate);
			
			
			$laporan = new stdClass();
			$laporan->paket_jasa = 0;
			$laporan->utama_barang = 0;
			$laporan->utama_jasa = 0;
			$laporan->utama_paket = 0;
			$laporan->total_penjualan = 0;
			
			foreach($penjualan as $pen){
				if(!isset($laporan->{$pen->jenis_produk})){
					$laporan->{$pen->jenis_produk} = (float) $pen->total;
				}
				$laporan->{$pen->jenis_produk} =  (float) $pen->total;
				$laporan->total_penjualan +=  (float) $pen->total;
			}
			foreach($laporan as &$lap){
				$lap = 'Rp '.number_format($lap,0,',','.');
			}
			
			$this->status = 100;
			$this->message = 'Berhasil';
			
			$data = $laporan;
		}else{
			$this->status = 449;
			$this->message = 'Salah satu parameter ada yang kurang';
		}
		
		$this->__json_out($data);
	}
}