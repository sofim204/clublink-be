<style>
	.text-left {
		text-align: left !important;
	}

	.hidden {
		display: none;
	}
</style>

<div id="modal_add_popular_club_to_homepage" class="modal fade" role="dialog" aria-hidden="true">
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
				<form id="form_add_popular_club_to_homepage" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-8">
								<label class="" for="select_popular_club">Select Popular Club</label><br>
								<select id="select_popular_club" class="form-control" style="width: 400px;">
									<option value="" selected="selected">Select Popular Club</option>
								</select>
								<input id="club_choosed_id" type="text" name="custom_id" class="form-control" size="36" autocomplete="off" required />
							</div>
							<div class="col-md-4" style="margin-top: 60px;">
								<label class="" for=""></label>
								<a class="button_check_club" style="cursor: pointer;">Check Club?</a>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<label class="" for="priority_text">Priority</label><br>
								<input id="priority_text" type="number" name="priority" class="form-control" autocomplete="off" required />
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

<div id="modal_change_club" class="modal fade" role="dialog" aria-hidden="true">
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
				<form id="form_change_club" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="current_club_name">Current Club</label>
								<input id="current_club_name" type="text" class="form-control" autocomplete="off" disabled />
							</div>
							<div class="col-md-4" style="margin-top: 30px;">
								<label class="" for=""></label>
								<!-- <a id="button_change_club" style="cursor: pointer;">Change Club?</a> -->
								<a class="button_check_club" style="cursor: pointer;"><span style="font-size: 14px;">Check Club?</span></a>
							</div>
						</div>
						<div class="form-group container-change-club" style="display: none;">
							<div class="col-md-8">
								<!-- <label class="" for="select_club">Select Club</label><br>
								<select id="select_club" class="form-control" style="width: 400px;">
									<option value="" selected="selected">Select Club</option>
								</select> -->
								<input id="club_choosed" type="text" name="custom_id" class="form-control" autocomplete="off" />
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

<!-- <div id="modal_change_and_reorder_popular_club" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width: 600px; margin-top: 200px;">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Change All</strong></h2>
			</div>

			<div class="modal-body">
				<form id="form_change_and_reorder_popular_club" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-8">
								<label class="select_club">Select Club</label><br>
								<select id="select_club" class="form-control" style="width: 400px;">
									<option value="" selected="selected">Select Club</option>
								</select>
								<input id="club_choosed" type="hidden" name="i_group_id" class="form-control" autocomplete="off" required />
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
		</div>
	</div>
</div> -->