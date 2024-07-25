<style>
	.text-left {
		text-align: left !important;
	}
</style>

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
					<div class="col-xs-12 btn-group-vertical ">
						<a id="aedit" href="#" class="btn btn-info text-left" style="text-align: left;"><i class="fa fa-pencil"></i> Edit</a>
						<button id="bhapus" type="button" class="btn btn-info text-left" style="text-align: left;"><i class="fa fa-trash-o"></i> Hapus</button>
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

<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" role="dialog" aria-hidden="true">
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
							<div class="col-md-12">
								<label class="" for="inama">Nama *</label>
								<input id="inama" type="text" name="nama" class="form-control" minlength="1" placeholder="Nama target" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="ideskripsi">Deskripsi</label>
								<textarea id="ideskripsi" name="deskripsi" class="form-control" rows="5" placeholder="Penjelasan Target"></textarea>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="form-group">
							<div class="col-md-6" >
								<label class="control-label" for="isdate">Tgl Mulai </label>
								<input id="isdate" type="text" name="sdate" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" minlength="1" placeholder="" />
							</div>
							<div class="col-md-6" >
								<label class="control-label" for="iedate">S.D. Tgl </label>
								<input id="iedate" type="text" name="edate" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" minlength="1" placeholder="" />
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="form-group">
							<div class="col-md-6">
								<label for="iis_closed">Is Achieved?</label>
								<select id="iis_closed" name="is_closed" class="form-control">
									<option value="0">Belum</option>
									<option value="1">Sudah</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="iis_active">Aktif</label>
								<select id="iis_active" name="is_active" class="form-control">
									<option value="1">Iya</option>
									<option value="0">Tidak</option>
								</select>
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
							<div class="col-md-12">
								<img id="ieig_media" src="" class="img-responsive" />
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="form-group">
							<div class="col-md-6" >
								<label for="iepriority" class="control-label" >Urutan</label>
								<input id="iepriority" type="number" name="priority" class="form-control " placeholder="" />
							</div>
							<div class="col-md-6">
								<label for="ieis_active" class="control-label" >Aktif</label>
								<select id="ieis_active" name="is_active" class="form-control">
									<option value="1">Iya</option>
									<option value="0">Tidak</option>
								</select>
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
