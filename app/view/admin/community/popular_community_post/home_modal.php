<style>
	.text-left {
		text-align: left !important;
	}

	.hidden {
		display: none;
	}
</style>

<div id="modal_change_community" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width: 600px; margin-top: 200px;">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="form_change_community" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="current_community_name">Current Community</label>
								<input id="current_community_name" type="text" name="" class="form-control" autocomplete="off" disabled />
							</div>
							<div class="col-md-4" style="margin-top: 30px;">
								<label class="" for=""></label>
								<!-- <a id="button_change_community" style="cursor: pointer;">Change Community?</a> -->
								<a id="button_check_community_edit" style="cursor: pointer;"><span style="font-size: 14px;">Check Community Post?</span></a>
							</div>
						</div>
						<div class="form-group container-change-community" style="display: none;">
							<div class="col-md-8">
								<label class="" for="select_community">Select Community</label><br>
								<select id="select_community" class="form-control" style="width: 400px;">
									<option value="" selected="selected">Select Community</option>
								</select>
								<input id="community_choosed" type="hidden" name="custom_id" class="form-control" autocomplete="off" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<label class="" for="change_priority">Priority</label><br>
								<input id="change_priority" type="number" name="priority" class="form-control" autocomplete="off" />
							</div>
							<div class="col-md-4">
								<label class="" for="change_is_active">Status</label><br>
								<select id="change_is_active" name="is_active" class="form-control" required>
									<option value="1">Active</option>
									<option value="0">Inactive</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<label class="" for="iecdate">Start Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="iecdate" type="text" name="start_date" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" readonly="readonly" required />
								</div>
							</div>
							<div class="col-md-4">
								<label class="" for="ieedate">End Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="ieedate" type="text" name="end_date" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" readonly="readonly" required />
								</div>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="btn-save" type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Change</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<div id="modal_add_popular_community_to_homepage" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width: 600px; margin-top: 200px;">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="form_add_popular_community_to_homepage" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-8">
								<label class="" for="select_popular_community">Input Community Id</label><br>
								<input id="community_choosed_id" type="text" name="custom_id" class="form-control" size="36" autocomplete="off" required />
							</div>
							<div class="col-md-4" style="margin-top: 30px;">
								<label class="" for=""></label>
								<a id="button_check_community" style="cursor: pointer;">Want to Check Post?</a>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label class="" for="priority_text">Priority</label><br>
								<input id="priority_text" type="number" name="priority" class="form-control" autocomplete="off" required />
							</div>
							<div class="col-md-4">
								<label class="" for="icdate">Start Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="icdate" type="text" name="start_date" class="form-control input-datepicker add_date" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" readonly="readonly" required />
								</div>
							</div>
							<div class="col-md-4">
								<label class="" for="iedate">End Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="iedate" type="text" name="end_date" class="form-control input-datepicker add_date" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" readonly="readonly" required />
								</div>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="btn-save" type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<div id="container_change_and_reorder_popular_community" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width: 600px; margin-top: 200px;">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Change All</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="form_change_and_reorder_popular_community" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-8">
								<label class="select_community">Select Community</label><br>
								<select id="select_community" class="form-control" style="width: 400px;">
									<option value="" selected="selected">Select Community</option>
								</select>
								<input id="community_choosed" type="hidden" name="i_group_id" class="form-control" autocomplete="off" required />
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="btn-save" type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Change</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>