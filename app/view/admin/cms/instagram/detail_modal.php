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
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
						<button id="bhapus" type="button" class="btn btn-info text-left"><i class="fa fa-trash-o"></i> Hapus</button>
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
					<input type="hidden" name="e_target_id" value="<?=$detail->id?>" />
					<fieldset>
						<div class="form-group">
							<div class="col-md-9">
								<label class="" for="inama">Nama *</label>
								<input id="inama" type="text" name="nama" class="form-control" minlength="1" placeholder="Nama" required />
							</div>
							<div class="col-md-3">
								<label class="" for="iprioritas">Urutan</label>
								<input id="iprioritas" type="number" name="prioritas" class="form-control" value="0" placeholder="Nama" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5">
								<label for="iharga">Harga</label>
								<input id="iharga" type="text" class="form-control uang-rupiah" placeholder="harga" value="0" />
								<input id="ihharga" name="harga" type="hidden"  />
							</div>
							<div class="col-md-2">
								<label for="iqty">Qty</label>
								<input id="iqty" name="qty" type="number" class="form-control" placeholder="Jml" value="1" />
							</div>
							<div class="col-md-5">
								<label for="isubtotal">Subtotal</label>
								<input id="isubtotal" type="text" class="form-control uang-rupiah" placeholder="subtotal" value="0" disabled />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="iis_closed">Is Achieved?</label>
								<select id="iis_closed" name="is_closed" class="form-control">
									<option value="0">Belum</option>
									<option value="1">Sudah</option>
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
					<input type="hidden" name="e_target_id" value="<?=$detail->id?>" />
					<fieldset>
						<div class="form-group">
							<div class="col-md-9">
								<label class="" for="ienama">Nama *</label>
								<input id="ienama" type="text" name="nama" class="form-control" minlength="1" placeholder="Nama" required />
							</div>
							<div class="col-md-3">
								<label class="" for="ieprioritas">Urutan</label>
								<input id="ieprioritas" type="number" name="prioritas" class="form-control" value="0" placeholder="urutan" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5">
								<label for="ieharga">Harga *</label>
								<input id="ieharga" type="text" class="form-control uang-rupiah" placeholder="harga" value="0" />
								<input id="iehharga" name="harga" type="hidden"  />
							</div>
							<div class="col-md-2">
								<label for="ieqty">Qty</label>
								<input id="ieqty" name="qty" type="number" class="form-control" placeholder="Jml" value="1" />
							</div>
							<div class="col-md-5">
								<label for="iesubtotal">Subtotal</label>
								<input id="iesubtotal" type="text" class="form-control uang-rupiah" placeholder="subtotal" value="0" disabled />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ieis_closed">Is Achieved?</label>
								<select id="ieis_closed" name="is_closed" class="form-control">
									<option value="0">Belum</option>
									<option value="1">Sudah</option>
								</select>
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
