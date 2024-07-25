<style>
	table {
		width: 100%;
	}

	th, td {
		padding: 8px;
		text-align: left;
		border-bottom: 1px solid #ddd;
	}

	table#drTable tr:hover {
		background-color: #EFBF65;
	}

	/* refer to https://stackoverflow.com/questions/60149994/how-to-add-a-x-to-clear-input-field 
		by Muhammad Sofi 27 December 2021 18:00 | Add x button to clear search box 
	*/
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}

	/* change input datepicker readonly background color */
	#ifstart_date {
		background-color: #FFFFFF;
	}
</style>
<div id="page-content">
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="list_community" class="btn btn-info"><i class="fa fa-plus"></i> Select Community</a>
				</div>
			</div>
		</div>
	</div>		
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>Highlight</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong>Filter</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-2">
					<label for="ifstart_date">Submit Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="ifstart_date" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="Submit date" autocomplete="off" readonly /> <!-- fix autocomplete after select a date -->
					</div>
				</div>
				<div class="col-md-3">
					<label for="select_general_location">General Location</label>
					<select id="select_general_location" class="form-control"></select>
				</div>
				<div class="col-md-1">
					<button id="reset-filter" class="btn btn-block btn-danger">Clear</button>
				</div> 
				<div class="col-md-1">
					<input class="form-control" type="hidden" name="id_kelurahan" id="id_kelurahan" style="background:#E7E7E7" disabled>
				</div>
			</div>
		</div>
	</div>
	<div class="block full">
		<div class="block-title">
			<h2><strong>Highlight Community List</strong></h2>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" style="width:100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th class="text-center">No.</th>
						<th class="text-center">#</th>
						<th>Submit Date</th>
						<th>Title</th>
						<th width="650px">Description</th>
						<th width="200px">User</th>
						<th>Status</th>
						<th>General Location</th>
						<th>Priority</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
</div>