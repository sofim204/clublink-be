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
				<h2 class="modal-title">Add</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
            <div class="form-group">
              <div class="col-md-12">
                <label class="" for="ilength_max" >length max*</label>
                <input id="ilength_max" type="number" step="0.01" name="length_max" class="form-control" minlength="1" placeholder="" required />
              </div>
							<div class="col-md-12">
								<label class="" for="ilengthunit" >length Unit*</label>
								<input id="ilengthunit" type="text" name="length_unit" class="form-control" minlength="1" placeholder="" required />
							</div>
							<div class="col-md-12">
								<label class="" for="icost" >cost*</label>
								<input id="icost" type="number" step="0.01" name="cost" class="form-control" minlength="1" placeholder="" required />
							</div>
            </div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
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
							<div class="col-md-6">
								<label class="" for="ienation_code">Nation Code</label>
								<input id="ienation_code" type="number" name="nation_code" class="form-control" disabled>
							</div>
							<div class="col-md-6">
								<label class="" for="ieid">ID</label>
								<input id="ieid" type="number" name="id" class="form-control" disabled>
							</div>
						</div>
						<div class="form-group">
              <div class="col-md-12">
                <label class="" for="ielength_max" >length max*</label>
                <input id="ielength_max" type="number" step="0.01" name="length_max" class="form-control" minlength="1" placeholder="" required />
              </div>
							<div class="col-md-12">
								<label class="" for="ielengthunit" >length Unit*</label>
								<input id="ielengthunit" type="text" name="length_unit" class="form-control" minlength="1" placeholder="" required />
							</div>
							<div class="col-md-12">
								<label class="" for="iecost" >cost*</label>
								<input id="iecost" type="number" step="0.01" name="cost" class="form-control" minlength="1" placeholder="" required />
							</div>
            </div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="bhapus" type="button" class="btn btn-sm btn-warning">Delete</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
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
				<h2 class="modal-title">Option</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
						<button id="ahapus" type="button" class="btn btn-danger text-left"><i class="fa fa-trash-o"></i> Delete</button>
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
