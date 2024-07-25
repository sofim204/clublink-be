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
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="idevice" >Device: *</label>
								<select id="idevice" name="device" class="form-control gap-per-row" required>
									<option value="">Select device</option>
									<option value="android">Android</option>
									<option value="ios">iOS</option>
								</select>
							</div>
							<div class="col-md-12">
								<label class="" for="iversion" >Name Version: *</label>
								<input id="iversion" type="text" name="version" class="form-control gap-per-row" minlength="1" placeholder="" required />
							</div>
							<div class="col-md-12">
								<label class="" for="istatus" >Type update: *</label>
								<select id="istatus" name="status" class="form-control gap-per-row" required>
										<option value="">Select type update</option>
										<option value="1">Minor</option>
										<option value="2">Major</option>
								</select>
							</div>
							<div class="col-md-12">
								<label class="" for="iactive" >Active or Not?: *</label>
								<select id="iactive" name="is_active" class="form-control gap-per-row" required>
										<option value="">Select activated</option>
										<option value="1">Active</option>
										<option value="0">Not Active</option>
								</select>
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
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit</strong></h2>
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
								<label class="" for="iedevice" >Device: *</label>
								<select id="iedevice" name="device" class="form-control gap-per-row" required>
									<option value="">Select device</option>
									<option value="android">Android</option>
									<option value="ios">iOS</option>
								</select>
							</div>
							<div class="col-md-12">
								<label class="" for="ieversion" >Name Version: *</label>
								<input id="ieversion" type="text" name="version" class="form-control gap-per-row" minlength="1" placeholder="" required/>
							</div>
							<div class="col-md-12">
								<label class="" for="iestatus" >Type update: *</label>
								<select id="iestatus" name="status" class="form-control gap-per-row" required>
										<option value="">Select type update</option>
										<option value="1">Minor</option>
										<option value="2">Major</option>
								</select>
							</div>
							<div class="col-md-12">
								<label class="" for="ieactive" >Active or Not?: *</label>
								<select id="ieactive" name="is_active" class="form-control gap-per-row" required>
										<option value="">Select activated</option>
										<option value="1">Active</option>
										<option value="0">Not Active</option>
								</select>
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
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Options</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
						<div style="margin-bottom: 5px;"></div>
						<button id="ahapus" type="button" class="btn btn-danger text-left"><i class="fa fa-trash-o"></i> Delete</button>
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
