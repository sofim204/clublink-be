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
								<label for="ikode">Kode</label>
								<input id="ikode" type="text" name="kode" class="form-control" minlength="2" maxlength="3" placeholder="Kode" />
							</div>
							<div class="col-md-8">
								<label class="" for="inama">Nama*</label>
								<input id="inama" type="text" name="nama" class="form-control" minlength="1" placeholder="" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="iharga">Harga*</label>
								<input id="iharga" type="text" name="harga" class="form-control" minlength="1" placeholder="" required/>
							</div>
              <div class="col-md-6">
								<label for="iharga_rp">Harga Rupiah*</label>
								<input id="iharga_rp" type="text" name="harga_rp" class="form-control" minlength="1" placeholder="" required />
							</div>
            </div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ikurir_default">Kurir Default</label>
								<input id="ikurir_default" type="text" name="kurir_default" class="form-control" minlength="1" ></input>
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
								<label for="iekode">Kode</label>
								<input id="iekode" type="text" name="kode" class="form-control" minlength="2" maxlength="3" placeholder="" />
								<input id="ieid" name="id" type="hidden" value="" />
							</div>
							<div class="col-md-8">
								<label class="" for="ienama">Nama*</label>
								<input id="ienama" type="text" name="nama" class="form-control" minlength="1" placeholder="" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ieharga">Harga*</label>
								<input id="ieharga" type="text" name="harga" class="form-control" minlength="1" placeholder="" required/>
							</div>
              <div class="col-md-6">
								<label for="ieharga_rp">Harga Rupiah*</label>
								<input id="ieharga_rp" type="text" name="harga_rp" class="form-control" minlength="1" placeholder="" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="iekurir_default">Kurir Default</label>
								<input id="iekurir_default" type="text" name="kurir_default" class="form-control" ></input>
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
						<a id="ahapus" type="button" class="btn btn-info text-left"><i class="fa fa-trash-o"></i> Hapus</a>
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
