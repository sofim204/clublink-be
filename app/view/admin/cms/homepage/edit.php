<style>
	#modal_grupjenis_table tbody tr {
		cursor: pointer;
	}
	#ikode_before {
		cursor: pointer;
	}
	@media only screen and (max-width: 768px) {
		{
			.form-inline .input-group>.form-control
		}
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?php echo base_url_admin('ecommerce/flashsale/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Kembali</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Produk Master</li>
		<li>Jasa</li>
		<li>Edit</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="fedit" action="<?php echo base_url_admin('ecommerce/flashsale/tambah/'); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<input type="hidden" id="ieproduk_komposisi" name="produk_komposisi" value="{}" />
		<div class="block full row">
			<div class="block-title">
				<h2><strong>Data Utama</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="iea_company_id">Tersedia Di *</label>
					<select id="iea_company_id" name="a_company_id" class="form-control">
						<option value="NULL" data-kode="00">Semua Cabang</option>
						<?php if(isset($cabang)){ foreach($cabang as $cbg){ ?>
						<option value="<?php echo $cbg->id; ?>" data-kode="<?=$cbg->kode?>" <?php if($cbg->id == $jasa->a_company_id) echo 'selected'; ?>><?php echo $cbg->nama; ?></option>
						<?php }} ?>
					</select>
				</div>
				<div class="col-md-3">
					<label for="ieb_kategori_id">Kategori *</label>
					<select id="ieb_kategori_id" name="b_kategori_id" class="form-control">
						<option value="null" data-kode="00"> - </option>
						<?php if(isset($kategori)){ foreach($kategori as $kat){
							if(!isset($kat->id)) $kat->id = 'null';
							if(!isset($kat->kode)) $kat->kode = '00';
							if(!isset($kat->nama)) $kat->nama = '';
						?>
						<option value="<?php echo $kat->id; ?>" data-kode="<?=$kat->kode?>" <?php if($kat->id == $jasa->b_kategori_id) echo 'selected'; ?>><?php echo $kat->nama; ?></option>
						<?php if(count($kat->childs)){ foreach($kat->childs as $kc){
							if(!isset($kc->id)) $kc->id = 'null';
							if(!isset($kc->kode)) $kc->kode = '00';
							if(!isset($kc->nama)) $kc->nama = '';
						?>
						<option value="<?php echo $kc->id; ?>" data-kode="<?=$kc->kode?>" <?php if($kc->id == $jasa->b_kategori_id) echo 'selected'; ?>>--&nbsp;<?php echo $kc->nama; ?></option>
						<?php }}}} ?>
					</select>
				</div>
				<div class="col-md-3">
					<label for="ietindakan_oleh">Tindakan Oleh *</label>
					<select id="ietindakan_oleh" name="tindakan_oleh" class="form-control" required>
						<option value="semua" <?php if("semua" == $jasa->tindakan_oleh) echo 'selected'; ?>>-</option>
						<option value="dokter" <?php if("dokter" == $jasa->tindakan_oleh) echo 'selected'; ?>>Dokter</option>
						<option value="perawat" <?php if("perawat" == $jasa->tindakan_oleh) echo 'selected'; ?>>Perawat</option>
						<option value="terapis" <?php if("terapis" == $jasa->tindakan_oleh) echo 'selected'; ?>>Terapis</option>
						<option value="semua" <?php if("semua" == $jasa->tindakan_oleh) echo 'selected'; ?>>Semua</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="ieis_asistensi">Butuh Asistensi *</label>
					<select id="ieis_asistensi" name="is_asistensi" class="form-control" required>
						<option value="0" <?php if("0" == $jasa->is_asistensi) echo 'selected'; ?>>Tidak</option>
						<option value="1" <?php if("1" == $jasa->is_asistensi) echo 'selected'; ?>>Iya</option>
					</select>
				</div>

			</div>
			<div class="form-group"><div class="col-md-12"><br></div></div>
			<div class="form-group">

				<div class="col-md-3">
					<label class="" for="iesku">SKU *</label>
					<input id="iesku" type="text" name="sku" class="form-control" minlength="1" placeholder="SKU/kode jasa" value="<?=$jasa->sku?>" required />
				</div>

				<div class="col-md-9">
					<label class="" for="ienama">Nama Jasa *</label>
					<input id="ienama" type="text" name="nama" class="form-control" minlength="1" placeholder="Nama Produk" value="<?=$jasa->nama?>" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Harga</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="ieharga_jual">Harga Jual*</label>
					<input id="ieharga_jual" type="text" name="harga_jual" class="form-control" placeholder="harga jual" value="<?=$jasa->harga_jual?>" required />
					<input id="iehharga_jual" type="hidden" name="harga_jual" class="form-control" placeholder="harga jual" value="<?=$jasa->harga_jual?>" />
				</div>
			</div>
		</div>


		<div class="block full row">
			<div class="block-title">
				<div class="block-options pull-right">
					<a href="#" class="btn btn-alt btn-sm btn-default btn-hidden-block" >
						<i class="fa fa-minus"></i>
					</a>
				</div>
				<h2><strong>Deskripsi</strong></h2>
			</div>
			<div class="form-group" style="display:none;">
				<div class="form-group">
					<div class="col-md-12">
						<textarea id="iedeskripsi" name="deskripsi" class="ckeditor" rows="5"><?=$jasa->deskripsi?></textarea>
					</div>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Poin</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="iepoin_terapis">Poin Terapis</label>
					<input id="iepoin_terapis" name="poin_terapis" type="text" class="form-control" value="<?=$jasa->poin_terapis?>" />
				</div>
				<div class="form-group">
					<div class="col-md-4">
						<label for="iepoin_asistensi">Poin Asistensi</label>
						<input id="iepoin_asistensi" name="poin_asistensi" type="text" class="form-control" value="<?=$jasa->poin_asistensi?>" />
					</div>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Penting</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3" style="display:none;">
					<label for="iescope">Lingkup</label>
					<select id="iescope" name="scope" class="form-control">
						<option value="current_only" data-kode="CO" <?php if("current_only" == $jasa->scope) echo 'selected'; ?>>Cabang Saja</option>
						<option value="all" data-kode="AL" <?php if("all" == $jasa->scope) echo 'selected'; ?>>Semua Cabang</option>
						<option value="current_below" data-kode="CB" <?php if("current_below" == $jasa->scope) echo 'selected'; ?> style="display:none;">Cabang Ini dan dibawahnya</option>
						<option value="none" data-kode="NN" <?php if("none" == $jasa->scope) echo 'selected'; ?> style="display:none;">Tidak Ada</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="ieis_visible">Tampil</label>
					<select id="ieis_visible" name="is_visible" class="form-control">
						<option value="1" <?php if("1" == $jasa->is_visible) echo 'selected'; ?>>Iya</option>
						<option value="0" <?php if("0" == $jasa->is_visible) echo 'selected'; ?>>Tidak</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="ieis_active">Status</label>
					<select id="ieis_active" name="is_active" class="form-control">
						<option value="1" <?php if("1" == $jasa->is_active) echo 'selected'; ?>>Aktif</option>
						<option value="0" <?php if("0" == $jasa->is_active) echo 'selected'; ?>>Tidak</option>
					</select>
				</div>
			</div>
		</div>

		<div id="paket_wrapper" class="block full row">
			<div class="block-title">
				<div class="block-options pull-right">
					<button id="bkomposisi_produk_reset" type="button" class="btn btn-alt btn-sm btn-default">Reset Komposisi</button>
				</div>
				<h2><strong>Komposisi Barang</strong></h2>
			</div>
			<div id="" class="row" style="margin-bottom:1em;">
				<div class="col-md-8">&nbsp;</div>
				<div class="col-md-4">
					<div class="input-group">
						<select id="filter_b_kategori_id" class="form-control">
							<option value="">Semua Kategori</option>
							<?php if(isset($kategori)){ foreach($kategori as $kat){ ?>
							<option value="<?php echo $kat->id; ?>" data-kode="<?=$kat->kode?>"><?php echo $kat->nama; ?></option>
							<?php if(count($kat->childs)){ foreach($kat->childs as $kc){ ?>
							<option value="<?php echo $kc->id; ?>" data-kode="<?=$kc->kode?>">--&nbsp;<?php echo $kc->nama; ?></option>
							<?php }}}} ?>
						</select>
						<span class="input-group-btn">
							<a id="filter_proses" href="#" class="btn btn-default">Filter</a>
						</span>
					</div>
				</div>
			</div>
			<div id="" class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table id="tableProduk" class="table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Kategori</th>
									<th>Kode</th>
									<th>Nama</th>
									<th>Jumlah</th>
									<th>Satuan</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<div class="block-options pull-right">
					<button id="bgaleritambah" type="button" href="#" class="btn btn-alt btn-sm btn-default" data-toggle="tooltip" title="Pilih gambar" data-original-title="Pilih gambar"><i class="fa fa-plus"></i></button>
				</div>
				<h2><strong>Gambar</strong></h2>
			</div>
			<div id="dgaleri_items" class="row media-manager"></div>
		</div>


		<div class="block full row">
			<div class="block-title">
				<h2><strong>Action</strong></h2>
			</div>
			<div class="row">
				<div class="col-md-8">&nbsp;</div>
				<div class="col-md-4 text-right">
					<div class="btn-group"><input type="submit" value="Simpan Perubahan" class="btn btn-primary" />
					</div>
				</div>
			</div>
		</div>
	</form>
	<!-- END Content -->
</div>
