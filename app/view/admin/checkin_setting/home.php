<style>
	/* refer to https://stackoverflow.com/questions/60149994/how-to-add-a-x-to-clear-input-field 
		by Muhammad Sofi 27 December 2021 18:00 | Add x button to clear search box 
	*/
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}
	
	table#drTable tr:hover {
		background-color: #EFBF65;
	}

	.text-left {
		text-align: left;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<!-- <li>Misc</li> -->
		<li>Checkin Setting</li>
	</ul>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Checkin Setting</strong></h2>
		</div>
		<div class="block-section">
			<div class="row">
				<!-- <div class="col-md-2">
					<label for="flcdate_start">From Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="flcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" readonly />
					</div>
				</div>
				<div class="col-md-2">
					<label for="flcdate_end">To Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="flcdate_end" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" readonly />
					</div>
				</div> -->
				<!-- <div class="col-md-2">
					<label for="flpath">Path</label>
					<select id="flpath" class="form-control">
						<option value="">All</option>
						<option value="api_mobile">Api Mobile</option>
						<option value="api_cron">Api Cron</option>
						<option value="api_admin">Api Admin</option>
					</select>
				</div> -->
				<div class="col-md-2" style="float: right;">
					<button id="btn_create_new_event" class="btn btn-block btn-info">New Event</button>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th width="10px">No.</th>
						<th width="10px">ID</th>
						<th>Start Event</th>
						<th>End Event</th>
						<!-- <th>Period (Month)</th> -->
						<th>Create Date</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Point Policy</strong></h2>
		</div>
		<div class="block-section">
			<div class="row">
				<div class="col-md-4">
					<div class="block">
						<div class="row">
							<div class="col-md-12">
								<label for="" class="control-label"><h4><strong>Check In Setting</strong><h4></label>
							</div>
							<div class="col-md-12">
								<form action="<?=base_url('api_admin/checkin_setting/')?>checkin_daily" method="post" class="form-horizontal form-setup" >
									<div class="form-group">
										<div class="col-md-5">
											<label for="fs_leaderboard_point_remark_E1" class="control-label">Check In Daily</label>
										</div>
										<div class="col-md-7">
											<div class="input-group">
												<input id="fs_leaderboard_point_remark_E1" name="leaderboard_point_remark_E1" type="number" min="0" max="1000" maxlength="3" class="form-control" />
												<div class="input-group-btn">
													<button type="submit" class="btn btn-info"><i class="fa fa-save" aria-hidden="true"></i> Save</button>
												</div>
											</div>
										</div>
									</div>
								</form>
								<form action="<?=base_url('api_admin/checkin_setting/')?>checkin_weekly" method="post" class="form-horizontal form-setup" >
									<div class="form-group">
										<div class="col-md-5">
											<label for="fs_leaderboard_point_remark_E2" class="control-label">Check In Weekly</label>
										</div>
										<div class="col-md-7">
											<div class="input-group">
												<input id="fs_leaderboard_point_remark_E2" name="leaderboard_point_remark_E2" type="number" min="0" max="1000" maxlength="3" class="form-control" />
												<div class="input-group-btn">
													<button type="submit" class="btn btn-info"><i class="fa fa-save" aria-hidden="true"></i> Save</button>
												</div>
											</div>
										</div>
									</div>
								</form>
								<form action="<?=base_url('api_admin/checkin_setting/')?>checkin_monthly" method="post" class="form-horizontal form-setup" >
									<div class="form-group">
										<div class="col-md-5">
											<label for="fs_leaderboard_point_remark_E3" class="control-label">Check In Monthly</label>
										</div>
										<div class="col-md-7">
											<div class="input-group">
												<input id="fs_leaderboard_point_remark_E3" name="leaderboard_point_remark_E3" type="number" min="0" max="1000" maxlength="3" class="form-control" />
												<div class="input-group-btn">
													<button type="submit" class="btn btn-info"><i class="fa fa-save" aria-hidden="true"></i> Save</button>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div> <!-- end left -->
				<div class="col-md-6">
				</div><!-- end right -->
			</div>
		</div>
	</div>
</div>
