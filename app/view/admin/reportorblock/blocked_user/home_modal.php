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
						<a id="btn_edit_data" class="btn btn-info text-left"><i class="fa fa-info-circle"></i> Edit</a>
						<a id="btn_delete_data" class="btn btn-danger text-left"><i class="fa fa-trash-o"></i> Delete</a>
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

<div id="modal_create_new_event" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add New Event</strong></h2>
			</div>
			<div class="modal-body">
				<form id="form_add_data" action="" method="post" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label for="start_date_event">From Date</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="start_date_event" type="text" name="start_date" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" autocomplete="off" />
								</div>
							</div>
						</div>
						<div class="form-group" style="display: none;">	
							<div class="col-md-6">
								<label for="month_period">Period</label>
								<div class="input-group" style="width: 165px;">
									<input id="month_period" type="number" min="0" max="12" value="0" name="period" class="form-control" placeholder="x month" autocomplete="off"/>
								</div>
							</div>
						</div>
						<div class="form-group">	
							<div class="col-md-4">
								<label for="end_date_event">To Date</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="end_date_event" type="text" name="end_date" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" autocomplete="off" />
								</div>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" id="btn_save" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit Data Event</strong></h2>
			</div>
			<div class="modal-body">
				<form id="form_edit_data" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<!-- by Muhammad Sofi 3 February 2022 15:34 | add edit variable -->
						<div class="form-group">
							<div class="col-md-4">
								<input type="hidden" id="ieid">
								<label class="control-label" for="iestatus">Status</label>
								<select name="is_active" id="iestatus" class="form-control">
									<option value="1">Active</option>
									<option value="0">Inactive</option>
								</select>
							</div>
							<br>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<!-- <button type="button" id="bdelete" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Delete</button> -->
							<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Save Changes</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>