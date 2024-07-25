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
							<div class="col-md-6">
                <label class="" for="iprovinsi_id" >Provinsi ID</label>
								<select id="iprovinsi_id" name="provinsi_id" class="form-control" >
                  <?php foreach($provinsi as $p) { ?>
                  <option value="<?=$p->id?>"><?=$p->nama_provinsi?></option>
                  <?php }?>
                </select>
							</div>
							<div class="col-md-6">
								<label class="" for="inama_kabkota" >Nama Kabupaten*</label>
								<input id="inama_kabkota" type="text" name="nama_kabkota" class="form-control" minlength="1" placeholder="" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ilatitude">Latitude</label>
								<input id="ilatitude" type="text" name="longitude" class="form-control" minlength="1" placeholder="" />
							</div>
              <div class="col-md-6">
								<label for="ilongitude">Longitude</label>
								<input id="ilongitude" type="text" name="longitude" class="form-control" minlength="1" placeholder="" />
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
							<div class="col-md-6">
                <label class="" for="ieprovinsi_id">Provinsi ID</label>
								<select id="ieprovinsi_id" name="provinsi_id" class="form-control" >
                  <?php foreach($provinsi as $p) { ?>
                  <option value="<?=$p->id?>"><?=$p->nama_provinsi?></option>
                  <?php }?>
                </select>
							</div>
							<div class="col-md-6">
								<label for="ienama_kabkota" >Nama Kabupaten*</label>
								<input id="ienama_kabkota" type="text" name="nama_kabkota" class="form-control" minlength="1" placeholder="" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ielatitude">Latitude</label>
								<input id="ielatitude" type="text" name="latitude" class="form-control" minlength="1" placeholder="" />
							</div>
							<div class="col-md-6">
								<label for="ielongitude">Longitude</label>
								<input id="ielongitude" type="text" name="longitude" class="form-control" minlength="1" placeholder="" />
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
