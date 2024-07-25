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

	.swal2-popup {
		font-size: 1.2rem !important;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<li>Seme Log Activity</li>
	</ul>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Seme Log Activity</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-2">
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
				</div>
				<div class="col-md-2">
					<label for="flpath">Path</label>
					<select id="flpath" class="form-control">
						<option value="">All</option>
						<option value="api_mobile">Api Mobile</option>
						<option value="api_cron">Api Cron</option>
						<option value="api_admin">Api Admin</option>
					</select>
				</div>
				<div class="col-md-1">
					<button id="reset-filter" class="btn btn-block btn-danger">Clear</button>
				</div>
				<div class="col-md-2">
					<button id="btn_delete_log" class="btn btn-block btn-info">Delete Log</button>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr>
						<th width="10px">No.</th>
						<th width="20px">ID</th>
						<th>Date</th>
						<th>Path</th>
						<th>Log</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
