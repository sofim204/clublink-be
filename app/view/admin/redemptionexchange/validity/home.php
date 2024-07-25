<style>
	/* refer to https://stackoverflow.com/questions/60149994/how-to-add-a-x-to-clear-input-field 
		by Muhammad Sofi 27 December 2021 18:00 | Add x button to clear search box 
	*/
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}

	/* change input datepicker readonly background color */
	#ifcdate_start, #ifcdate_end {
		background-color: #FFFFFF;
	}

	table#drTable tr:hover {
		background-color: #EFBF65;
	}
</style>
<div id="page-content">
	<ul class="breadcrumb breadcrumb-top">
        <li>Admin</li>
		<li>Redemption Exchange</li>
		<li>Awaiting Confirmation</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block">
		<div class="block-title">
			<h2><strong>Filter</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-2">
					<label for="ifcdate_min">From Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="ifcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" readonly />
					</div>
				</div>
				<div class="col-md-2">
					<label for="ifcdate_max">To Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="ifcdate_end" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" readonly />
					</div>
				</div>
				<!-- <div class="col-md-3" style="margin-bottom:6px;">
					<label for="select_customer">User</label>
					<select id="select_customer" class="form-control">
						<option value="">-- Select User --</option>
					</select>
				</div> -->
				<div class="col-md-3">
					<label for="ifstatus">Status</label>
					<select id="ifstatus" class="form-control">
						<option value="">-- View All --</option>
						<option value="1">Accepted by Admin</option>
						<option value="2">Accepted (Insufficient Balance-2nd)</option>
						<option value="3">Rejected (Insufficient Balance-1st)</option>
						<option value="4">Rejected by Admin</option>
						<option value="5">Ongoing</option>
					</select>
				</div>
				<div class="col-md-1">
					<button id="apply-filter" type="button" class="btn btn-info btn-block"><i class="fa fa-filter"></i> Filter</button>
				</div>
				<div class="col-md-1">
					<!-- by Muhammad Sofi 27 December 2021 14:43 | Fix issue button clear cannot reset filter and reload table -->
					<button id="reset-filter" type="button" class="btn btn-warning btn-block">Reset</button>
				</div>
				<!-- <div class="col-md-1">
					<button id="refresh-table" class="btn btn-block btn-primary">Refresh</button>
				</div> -->
			</div>
		</div>
	</div>
	<div class="block full">
		<div class="block-title">
			<h2><strong>Awaiting Confirmation List</strong></h2>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th class="text-center">#</th>
						<th>ID</th>
						<th>Request Date</th>
						<th>Confirm Date</th>
						<th>Redemption Exchange</th>
						<th>Email</th>
						<th>Requester Name</th>
						<th>Telp</th>
                        <th>Cost SPT</th>
                        <th>Amount Get</th>
						<th>Status</th>
						<th>Active</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
</div>
