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
				<h2 class="modal-title">Options</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
						<a id="aicon_change" href="#" class="btn btn-info text-left"><i class="fa fa-image"></i> Change Icon</a>
						<button id="bhapus" type="button" class="btn btn-danger text-left"><i class="fa fa-trash-o"></i> Delete</button>
					</div>
				</div>
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
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
				<h2 class="modal-title">Add New</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="inama">Name *</label>
								<input id="inama" type="text" name="nama" class="form-control" placeholder="Name of Value" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="inilai">Value *</label>
								<input id="inilai" type="text" name="nilai" class="form-control" placeholder="Real Value" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iicon">Icon</label>
								<input id="iicon" type="file" name="icon" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<!--<div class="col-md-4">
								<label class="control-label" for="iprioritas">Priority * <small>1 = top</small></label>
								<input id="iprioritas" name="prioritas" type="number" class="form-control" value="0" />
							</div>-->
							<div class="col-md-4">
								<label class="control-label" for="iis_active">Status</label>
								<select id="iis_active" name="is_active" class="form-control">
									<option value="1">Active</option>
									<option value="0">Inactive</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Save</button>
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
				<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ienama">Name *</label>
								<input id="ienama" type="text" name="nama" class="form-control" placeholder="Name of Value" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ienilai">Value *</label>
								<input id="ienilai" type="text" name="nilai" class="form-control" placeholder="Real Value" required />
							</div>
						</div>
						<div class="form-group">
							<!--<div class="col-md-4">
								<label class="control-label" for="ieprioritas">Priority * <small>1 = top</small></label>
								<input id="ieprioritas" name="prioritas" type="number" class="form-control" value="0" />
							</div>-->
							<div class="col-md-4">
								<label class="control-label" for="ieis_active">Status</label>
								<select id="ieis_active" name="is_active" class="form-control">
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
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- modal icon change -->
<div id="modal_icon_change" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Change Icon</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ficon_change" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="ieimage_icon">Choose Icon File * <small>128px x 128px</small></label>
								<input id="ieimage_icon" type="file" name="icon" class="form-control"  required />
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-upload"></i> Upload</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>
