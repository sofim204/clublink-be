<style>
	.text-left {
	text-align: left !important;
	}
</style>
<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Tambah</h2>
			</div>
			<!-- END Modal Header -->
			
			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?php echo base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label class="" for="iorigin" >Origin</label>
								<select id="iorigin" name="origin" class="form-control" >
									<option value="Bandung">Kota Bandung</option>
								</select>
							</div>
							<div class="col-md-8">
								<label class="" for="ikode_jne" >Kode JNE*</label>
								<input id="ikode_jne" type="text" name="kode_jne" class="form-control" minlength="1" placeholder="" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<select id="ipropinsi" name="propinsi" class="form-control" >
									<option value=""></option>
									<?php foreach($provinsi as $p) { ?>
										<option value="<?=$p->id?>"><?=$p->nama_provinsi?></option>
									<?php }?>
								</select></div>
								<div class="col-md-4">
									<select id="ikabkota" name="kabkota" class="form-control" >
										<option value=""></option>
										<?php foreach($kabkota as $k) { ?>
											<option value="<?=$k->id?>"><?=$k->nama_kabkota?></option>
										<?php }?>
									</select>
								</div>
								<div class="col-md-4">
									<select id="ikecamatan" name="kecamatan" class="form-control" >
										<option value=""></option>
										<?php foreach($kecamatan as $kc) { ?>
											<option value="<?=$kc->id?>"><?=$kc->nama_kecamatan?></option>
										<?php }?>
									</select>
								</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="ioke15_tarif" >oke15_tarif</label>
								<input id="ioke15_tarif" type="text" name="oke15_tarif" class="form-control" minlength="1" placeholder="" />
							</div>
							<div class="col-md-6">
								<label class="" for="ioke15_est" >oke15_est</label>
								<input id="ioke15_est" type="text" name="oke15_est" class="form-control" minlength="1" placeholder="" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="ireg15_tarif" >reg15_tarif</label>
								<input id="ireg15_tarif" type="text" name="reg15_tarif" class="form-control" minlength="1" placeholder="" />
							</div>
							<div class="col-md-6">
								<label class="" for="ireg15_est" >reg15_est</label>
								<input id="ireg15_est" type="text" name="reg15_est" class="form-control" minlength="1" placeholder="" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="iyes15_tarif" >yes15_tarif</label>
								<input id="iyes15_tarif" type="text" name="yes15_tarif" class="form-control" minlength="1" placeholder="" />
							</div>
							<div class="col-md-6">
								<label class="" for="iyes15_est" >yes15_est</label>
								<input id="iyes15_est" type="text" name="yes15_est" class="form-control" minlength="1" placeholder="" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="istatus" >Status</label>
								<input id="istatus" type="text" name="status" class="form-control" minlength="1" placeholder="" />
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Simpan</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- modal edit -->
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Edit</h2>
			</div>
			<!-- END Modal Header -->
			
			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?php echo base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label class="" for="ieorigin" >Origin</label>
								<select id="ieorigin" name="origin" class="form-control" >
									<option value="Bandung">Kota Bandung</option>
								</select>
							</div>
							<div class="col-md-8">
								<label class="" for="iekode_jne" >Kode JNE*</label>
								<input id="iekode_jne" type="text" name="kode_jne" class="form-control" minlength="1" placeholder="" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<select id="iepropinsi" name="propinsi" class="form-control" required >
									<option value="">-- Pilih --</option>
								</select>
							</div>
							<div class="col-md-4">
								<select id="iekabkota" name="kabkota" class="form-control" required >
									<option value="">-- Pilih --</option>
								</select>
							</div>
							<div class="col-md-4">
								<select id="iekecamatan" name="kecamatan" class="form-control" required >
									<option value="">-- Pilih --</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="ieoke15_tarif" >oke15_tarif</label>
								<input id="ieoke15_tarif" type="text" name="oke15_tarif" class="form-control" minlength="1" placeholder="" />
							</div>
							<div class="col-md-6">
								<label class="" for="ieoke15_est" >oke15_est</label>
								<input id="ieoke15_est" type="text" name="oke15_est" class="form-control" minlength="1" placeholder="" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="iereg15_tarif" >reg15_tarif</label>
								<input id="iereg15_tarif" type="text" name="reg15_tarif" class="form-control" minlength="1" placeholder="" />
							</div>
							<div class="col-md-6">
							<label class="" for="iereg15_est" >reg15_est</label>
							<input id="iereg15_est" type="text" name="reg15_est" class="form-control" minlength="1" placeholder="" />
							</div>
							</div>
							<div class="form-group">
							<div class="col-md-6">
							<label class="" for="ieyes15_tarif" >yes15_tarif</label>
							<input id="ieyes15_tarif" type="text" name="yes15_tarif" class="form-control" minlength="1" placeholder="" />
							</div>
							<div class="col-md-6">
							<label class="" for="ieyes15_est" >yes15_est</label>
							<input id="ieyes15_est" type="text" name="yes15_est" class="form-control" minlength="1" placeholder="" />
							</div>
							</div>
							<div class="form-group">
							<div class="col-md-6">
							<label class="" for="iestatus" >Status</label>
							<input id="iestatus" type="text" name="status" class="form-control" minlength="1" placeholder="" />
							</div>
							</div>
							</fieldset>
							<div class="form-group form-actions">
							<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="bhapus" type="button" class="btn btn-sm btn-warning">Hapus</button>
							<button type="submit" class="btn btn-sm btn-primary">Simpan</button>
							</div>
							</div>
							</form>
							</div>
							<!-- END Modal Body -->
							</div>
							</div>
							</div>
							
							<!-- modal option -->
							<div id="modal_option" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog modal-sm">
							<div class="modal-content">
							
							<!-- Modal Header -->
							<div class="modal-header text-center">
							<h2 class="modal-title">Pilihan</h2>
							</div>
							<!-- END Modal Header -->
							
							<!-- Modal Body -->
							<div class="modal-body">
							<div class="row">
							<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
							<a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
							<button id="ahapus" type="button" class="btn btn-info text-left"><i class="fa fa-trash-o"></i> Hapus</button>
							</div>
							</div>
							<div class="row" style="margin-top: 1em; ">
							<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
							<div class="col-xs-12 btn-group-vertical">
							<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Tutup</button>
							</div>
							</div>
							<!-- END Modal Body -->
							</div>
							</div>
							</div>
							</div>
														