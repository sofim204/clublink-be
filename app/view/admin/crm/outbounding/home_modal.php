<style>
	.text-left {
		text-align: left !important;
	}
	.btn-add-field {
		display: flex; 
		flex-direction: column; 
		align-items: flex-start;
	}
</style>

<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add Marketing Outbound</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<input type="hidden" name="id" id="ieid" value="" />
								<label class="control-label" for="ijudul">Title *</label>
								<input id="ijudul" type="text" name="judul" class="form-control" minlength="1" placeholder="Marketing Outbound Title" required tabindex="1" autocomplete="off"/>
							</div>
							<div class="col-md-12">
								<label class="control-label" for="iteks">Text</label>
								<textarea id="iteks" name="teks" tabindex="2" class="form-control" rows="5" maxlength="2000"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="iproduct"><strong>Product Name and Link</strong></label>
								<div id="areaFieldProduct">
									<div class="input-group mb-3" style="display: flex; align-items: flex-end">
										<input type="text" name="product[]" class="form-control" placeholder="Product Name" value="" autocomplete="off">&nbsp;&nbsp;&nbsp;
										<input type="text" name="urlp[]" class="form-control" placeholder="Product Link" value="" autocomplete="off">
										<div class="input-group-append">
											<div style="width:80px;"></div>
										</div>
									</div>
									<div style="margin-bottom: 10px;"></div>
									<div id="newFieldProduct"></div>
								</div>
								<div class="btn-add-field">
									<button id="btnAddFieldProduct" class="btn btn-info" type="button">Add Field</button>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="ishop"><strong>Shop Name and Link</strong></label>
								<div id="areaFieldShop">
									<div class="input-group mb-3" style="display: flex; align-items: flex-end">
										<input type="text" name="shop[]" class="form-control" placeholder="Shop Name" value="" autocomplete="off">&nbsp;&nbsp;&nbsp;
										<input type="text" name="urls[]" class="form-control" placeholder="Shop Link" value="" autocomplete="off">
										<div class="input-group-append">
											<div style="width:80px;"></div>
										</div>
									</div>
									<div style="margin-bottom: 10px;"></div>
									<div id="newFieldShop"></div>
								</div>
								<div class="btn-add-field">
									<button id="btnAddFieldShop" class="btn btn-info" type="button">Add Field</button>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="iis_active">Status</label>
								<select id="iis_active" name="is_active" class="form-control" tabindex="23" required>
									<option value="0">Not Active</option>
									<option value="1">Active</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
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
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit Marketing Outbound</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?=base_url_admin(); ?>"  method="post" enctype="multipart/form-data"  class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iejudul">Title *</label>
								<input id="iejudul" type="text" name="judul" class="form-control" minlength="1" placeholder="Promo Title" required />
								<input id="ieis_notif" type="hidden" name="is_notif"/>
							</div>
							<div class="col-md-12">
								<label class="control-label" for="ieteks">Text</label>
								<textarea id="ieteks" name="teks" class="form-control" rows="5"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="ieis_active">Status</label>
								<select id="ieis_active" name="is_active" class="form-control" required>
									<option value="1">Active</option>
									<option value="0">Not Active</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>
<div id="modal_option" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Options</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="adetail" href="#" class="btn btn-warning text-left">
							<i class="fa fa-info-circle"></i> Detail
						</a>
					</div>
				</div>
				<div class="row" style="margin-top: 1em;">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
						<button id="bhapus" type="button" class="btn btn-danger btn-block text-left"><i class="fa fa-trash-o"></i> Delete</button>
					</div>
				</div>
				<div class="row" style="margin-top: 1em;">
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