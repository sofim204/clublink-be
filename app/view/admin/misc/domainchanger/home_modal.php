<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add New</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered">
					<fieldset>
						<div class="form-group" style="display:none;">
							<label class="col-md-4 control-label" for="ination_code">Nation Code*</label>
							<div class="col-md-8">
								<input id="ination_code" type="number" value="62" name="nation_code" class="form-control" minlength="2" maxlength="" placeholder=""  required readonly/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="iurl">Domain Name*</label>
							<div class="col-md-8">
								<input id="iurl" type="text" name="url" class="form-control" minlength="2" maxlength="" placeholder="" required />
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="form-group" style="display:none;">
							<label class="col-md-4 control-label" for="iis_active">Status*</label>
							<div class="col-md-8">
								<select id="iis_active" name="is_active" class="form-control" required>
									<option value="0">Inactive</option>
									<option value="1">Active</option>									
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<input id="btambah_submit" type="submit" value="Save Changes" class="btn btn-sm btn-primary" />
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
				<h2 class="modal-title"><strong>Change Status</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<input type="hidden" id="ieid1" name="id" value="" />
						<div class="form-group" style="display:none;">
							<label class="col-md-4 control-label" for="ienation_code">Nation Code*</label>
							<div class="col-md-8">
								<input id="ienation_code" type="number" name="nation_code" class="form-control" minlength="2" maxlength="" placeholder="" required readonly/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="ieurl">Domain Name</label>
							<div class="col-md-8">
								<input id="ieurl" type="text" name="url" class="form-control" minlength="2" maxlength="" placeholder="" required readonly/>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="form-group">
							<label class="col-md-4 control-label" for="ieis_active">Priority*</label>
							<div class="col-md-8">
								<select id="ieis_active" name="is_active" class="form-control" required>
									<option value="0">Unselected</option>
									<option value="1">Selected</option>									
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="bhapus" type="button" class="btn btn-sm btn-warning" style="display:none;">Delete</button>
							<button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!--modal edit password-->
<div id="modal_edit_password" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Change Password</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fpe" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<input type="hidden" id="fpe_id" name="id" />
						<input type="hidden" id="fpe_nation_code" name="nation_code" />
						<div class="form-group">
							<label class="col-md-4 control-label" for="inewpassword">Password*</label>
							<div class="col-md-8">
								<input id="fpe_newpassword" type="password" name="password" class="form-control" minlength="1" placeholder="" required />
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-4 control-label" for="irepassword">Re-Password*</label>
							<div class="col-md-8">
								<input id="fpe_renewpassword" type="password" class="form-control" minlength="1" placeholder="" required />
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!--Modal Option-->
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
					<div class="col-xs-12 btn-group-vertical ">
						<a id="adetail" href="#" class="btn btn-info text-left" style="text-align: left;display:none;"><i class="fa fa-list"></i> Detail</a>
						<a id="aedit" href="#" class="btn btn-info text-left" style="text-align: left;"><i class="fa fa-pencil"></i> Change Priority</a>
						<a id="aedit_password" href="#" class="btn btn-info text-left" style="text-align: left;display:none;"><i class="fa fa-asterisk"></i> Change Password</a>
						<button id="ahapus" type="button" class="btn btn-danger text-left" style="text-align: left;"><i class="fa fa-trash-o"></i> Delete</button>
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

<!--Modal detail-->
<div id="modal_detail" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Detail</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-bordered">
							<tr>
								<th>Username</th>
								<td>:</td>
								<td id="dta_username">-</td>
							</tr>
							<tr>
								<th>Password</th>
								<td>:</td>
								<td id="dta_password">-</td>
							</tr>
						</table>
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
