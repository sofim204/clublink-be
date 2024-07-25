<?php
class Seme_Custom extends SENE_Controller {
	public function index(){
	}
	protected function __customData(){
		$dt = new stdClass();
		$dt->c_produk_id = 'null';
		$dt->b_user_id = 'null';
		$dt->kode = '';
		$dt->custom_depan = 0;
		$dt->custom_depan_mode = 'teks';
		$dt->custom_depan_posisi = 'full';
		$dt->custom_depan_teks = '';
		$dt->custom_depan_warna = '';
		$dt->custom_depan_font = '';
		$dt->custom_depan_file = '';
		$dt->custom_depan_harga = 0.0;
		$dt->custom_belakang = 0;
		$dt->custom_belakang_mode = 'teks';
		$dt->custom_belakang_posisi = 'full';
		$dt->custom_belakang_teks = '';
		$dt->custom_belakang_warna = '';
		$dt->custom_belakang_font = '';
		$dt->custom_belakang_file = '';
		$dt->custom_belakang_harga = 0.0;
		$dt->custom_harga = 0.0;
		$dt->produk_jenis = '-';
		$dt->produk_kategori_nama = '-';
		$dt->produk_nama = '';
		$dt->produk_harga = 0.0;
		$dt->produk_berat = 0.0;
		return $dt;
	}
	protected function __check(){
		$d = $this->getKey();
		if(!is_object($d)) $d = new stdClass();
		if(!isset($d->custom)) $d->custom = array();
		if(!isset($d->custom[0])) $d->custom[0] = $this->__customData();
		$this->setKey($d);
		return $d;
	}
	public function get($key=""){
		$d = $this->__check();
		return $d->custom;
	}
	public function set($dt){
		$d = $this->__check();
		$d->custom[0] = $dt;
		return $d->custom;
	}
}