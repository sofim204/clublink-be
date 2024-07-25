<style>
	.gap-input-area {
		margin-top: 10px;
		/* background-color: #DBD5D1; */
		color: #000000;
		/* pointer-events: none; */
	}
</style>
<!-- modal edit -->
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<!-- <button type="button" class="close" data-dismiss="modal" style="background-color:#F2F2F2;"><strong><i class="fa fa-times"></i></strong></button> -->
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit Coverage Area</strong></h2>
			</div>
			<div class="modal-body">
				<form id="form_edit_data" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-2" style="margin-top: -8px;">
								<input type="hidden" id="ieid">
								<label class="control-label" for="ietype">Type Coverage</label>
								<select id="ietype" name="type" class="form-control">
									<option value="jalan">Jalan</option>
									<option value="kelurahan">Kelurahan</option>
									<option value="kecamatan">Kecamatan</option>
									<option value="kabkota">Kabupaten / Kota</option>
									<option value="provinsi">Provinsi</option>
								</select>
							</div>
							<div class="col-md-2" id="ietoggle_provinsi">
								<label for="select_ieprovinsi">Provinsi</label>
								<select id="select_ieprovinsi" class="form-control"></select>
								<input type="text" name="provinsi" id="ieprovinsi" class="form-control gap-input-area" autocomplete="off" />
							</div>
							<div class="col-md-2" id="ietoggle_kabkota">
								<label for="select_iekabkota">Kabupaten / Kota</label>
								<select id="select_iekabkota" class="form-control"></select>
								<input type="text" name="kabkota" id="iekabkota" class="form-control gap-input-area" autocomplete="off" />
							</div>
							<div class="col-md-2" id="ietoggle_kecamatan">
								<label for="select_iekecamatan">Kecamatan</label>
								<select id="select_iekecamatan" class="form-control"></select>
								<input type="text" name="kecamatan" id="iekecamatan" class="form-control gap-input-area" autocomplete="off" />
							</div>
							<div class="col-md-2" id="ietoggle_kelurahan">
								<label for="select_iekelurahan">Kelurahan</label>
								<select id="select_iekelurahan" class="form-control"></select>
								<input type="text" name="kelurahan" id="iekelurahan" class="form-control gap-input-area" autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iejalan">Jalan</label>
								<input type="text" name="jalan" id="iejalan" class="form-control" autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label class="control-label" for="ielatitude">Latitude</label>
								<input type="text" name="latitude" id="ielatitude" class="form-control" autocomplete="off" />
							</div>
							<div class="col-md-3">
								<label class="control-label" for="ielongitude">Longitude</label>
								<input type="text" name="longitude" id="ielongitude" class="form-control" autocomplete="off" />
							</div>
							<div class="col-md-3">
								<label class="control-label" for="ieradius">Radius</label>
								<input type="text" name="radius" id="ieradius" class="form-control" autocomplete="off" />
							</div>
							<div class="col-md-3">
								<label class="control-label" for="iestatus">Status</label>
								<select id="iestatus" name="is_active" class="form-control">
									<option value="1">Active</option>
									<option value="0">Inactive</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Save Changes</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>