<?php
class Penjualan extends JI_Controller{
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
			$orders = array();
			$penjualan = $this->dom->laporanPenjualanSelesai($mindate,$maxdate);
			
			//$this->debug($penjualan);
			//die();
			
			$laporan = new stdClass();
			$laporan->paket_jasa = 0;
			$laporan->utama_barang = 0;
			$laporan->utama_jasa = 0;
			$laporan->utama_paket = 0;
			$laporan->total_penjualan = 0;
			$laporan->total_ongkir = 0;
			$laporan->total_diskon = 0;
			$laporan->total_kodeunik = 0;
			$laporan->subtotal = 0;
			$laporan->total = 0;
			
			
			foreach($penjualan as $penj){
				if(!isset($orders[$penj->id])){
					$orders[$penj->id] = new stdClass();
					$laporan->total_ongkir += (float) $penj->ongkir;
					$laporan->total_penjualan += (float) $penj->sub_total;
					$laporan->total_diskon += (float) $penj->diskon_total;
					$laporan->subtotal += (float) $penj->sub_total;
					$laporan->total += (float) $penj->grand_total;
				}
				if(strtolower($penj->produk_jenis) == 'jasa'){
					$laporan->utama_jasa += ((float) $penj->harga_jadi * (int) $penj->qty);
					$laporan->paket_jasa += ((float) $penj->harga_jadi * (int) $penj->qty);
				}else if(strtolower($penj->produk_jenis) == 'barang'){
					$laporan->utama_barang += ((float) $penj->harga_jadi * (int) $penj->qty);
				}else{
					$laporan->utama_paket += ((float) $penj->harga_jadi * (int) $penj->qty);
				}
				$orders[$penj->id] = $penj;
			}
			
			$laporan->total_diskon = $laporan->total_diskon;
			$laporan->total = $laporan->total_penjualan - ($laporan->total_diskon + $laporan->total_ongkir);
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