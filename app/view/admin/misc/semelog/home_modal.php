<div id="modal_delete_log" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Delete Log</strong></h2>
			</div>
			<div class="modal-body">
				<form id="form_add_data" action="" method="post" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label for="cdate_delete_start">From Date</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="cdate_delete_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" readonly />
								</div>
							</div>
							<div class="col-md-4">
								<label for="cdate_delete_end">To Date</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="cdate_delete_end" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" readonly />
								</div>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" id="btn_delete_log_modal" class="btn btn-sm btn-danger"><i class="fa fa-save"></i> Delete</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>