<?php
class Ongkir extends JI_Controller{

	public function __construct(){
    parent::__construct();
		$this->setTheme('front');
		$this->load("api_mobile/a_negara_model",'anm');
		$this->load("api_mobile/d_provinsi_model",'apm');
		$this->load("api_mobile/d_kabkota_model",'dkm');
		$this->load("api_mobile/d_kecamatan_model",'dkecm');
		$this->load("api_mobile/e_jne_model",'jne');
		$this->load("api_mobile/e_jnt_model",'jnt');
	}
	private function __hStrip($a){
		return filter_var($a, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	}
	public function index(){
		$data = array();
		$ongkir = new stdClass();
		$ongkir->kode = "";
		$ongkir->nama = "";
		$ongkir->tarif = "0";
		$ongkir->estimasi = "2-7";
		$this->status = 444;
		$this->message = 'Parameter kecamatan_id tidak ada atau kosong';

		$kecamatan_id = $this->input->request("kecamatan_id");
		if(empty($kecamatan_id)){
			$kecamatan_id = (int) $this->input->request("kecamatan_id");
		}
		if($kecamatan_id>0){
			$alamat = $this->dkecm->getALamatLengkapByKecamatanId($kecamatan_id);
			if(isset($alamat->id)){
				$this->status = 200;
				$this->message = 'Success';
				$provinsi = $alamat->nama_provinsi;
				$kabkota = $alamat->nama_kabkota;
				$kecamatan = $alamat->nama_kecamatan;
				$jne = $this->jne->getOngkir($provinsi,$kabkota,$kecamatan);
				if(isset($jne->id)){
					if(!empty($jne->oke15_tarif)){
						$ongkir = new stdClass();
						$ongkir->kode = "OKE15";
						$ongkir->nama = "JNE OKE";
						$ongkir->tarif = $jne->oke15_tarif;
						$ongkir->estimasi = "4-14";
						$data[] = $ongkir;
					}
					if(!empty($jne->reg15_tarif)){
						$ongkir = new stdClass();
						$ongkir->kode = "REG15";
						$ongkir->nama = "JNE REG";
						$ongkir->tarif = $jne->reg15_tarif;
						$ongkir->estimasi = "4-7";
						$data[] = $ongkir;
					}
					if(!empty($jne->yes15_tarif)){
						$ongkir = new stdClass();
						$ongkir->kode = "YES15";
						$ongkir->nama = "JNE YES";
						$ongkir->tarif = $jne->yes15_tarif;
						$ongkir->estimasi = "2-7";
						$data[] = $ongkir;
					}
				}
				$jnt = $this->jnt->getOngkir($provinsi,$kabkota,$kecamatan);
				if(isset($jnt->reg_tarif)){
					if(!empty($jnt->reg_tarif)){
						$ongkir = new stdClass();
						$ongkir->kode = "JNTREG";
						$ongkir->nama = "JNT REG";
						$ongkir->tarif = $jnt->reg_tarif;
						$ongkir->estimasi = "4-14";
						$data[] = $ongkir;
					}
				}
			}else{
				$this->status = 499;
				$this->message = 'Alamat dengan kecamatan_id tersebut tidak dapat ditemukan';
			}
		}

		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "ongkir");
	}
	public function negara(){
		$this->status = 200;
		$this->message = 'Success';
		$data = $this->anm->get();
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "ongkir");
	}
	public function provinsi(){
		$this->status = 200;
		$this->message = 'Success';
		$data = $this->apm->get();
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "ongkir");
	}
	public function kabkota($provinsi_id=""){
		$this->status = 446;
		$this->message = 'Parameter provinsi_id tidak ada atau kosong';
		$data = array();

		$provinsi_id = (int) $provinsi_id;
		if(empty($provinsi_id)){
			$provinsi_id = (int) $this->input->request("provinsi_id");
		}
		if($provinsi_id>0){
			$this->status = 200;
			$this->message = 'Success';
			$data = $this->dkm->getByProvinsiId($provinsi_id);
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "ongkir");
	}
	public function kecamatan($kabkota_id=""){
		$this->status = 445;
		$this->message = 'Parameter kabkota_id tidak ada atau kosong';
		$data = array();

		$kabkota_id = (int) $kabkota_id;
		if(empty($provinsi_id)){
			$kabkota_id = (int) $this->input->request("kabkota_id");
		}
		if($kabkota_id>0){
			$this->status = 200;
			$this->message = 'Success';
			$data = $this->dkecm->getByKabkotaId($kabkota_id);
		}
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "ongkir");
	}
	public function gogovan(){
		$this->load("api_mobile/e_kurir_model","ekur");
		$this->status = 200;
		$this->message = 'Success';
		$data = array();
		$data['ongkir'] = $this->ekur->get();
		$this->__json_out($data, (isset($nation_code)) ? $nation_code : "", (isset($pelanggan->language_id)) ? $pelanggan->language_id : "", "ongkir");
	}
}
