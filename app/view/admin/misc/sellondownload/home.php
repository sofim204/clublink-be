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

	.pointer {
		cursor: pointer;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="btn_list_qrcode" href="<?=base_url_admin('misc/sellondownload/list_qrcode')?>" class="btn btn-info" style="margin-right: 10px;"><i class="fa fa-list"></i> List QR Code</a>
					<a id="btn_create_qrcode" href="javascript:void(0)" class="btn btn-info"><i class="fa fa-plus"></i> Create QR Code</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<li>Sellon Download</li>
	</ul>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Sellon Download</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-2">
					<label for="from_cdate">From</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="from_cdate" type="text" class="form-control input-datepicker pointer" data-date-format="yyyy-mm-dd" placeholder="From date" readonly />
					</div>
				</div>
				<div class="col-md-2">
					<label for="to_cdate">To</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="to_cdate" type="text" class="form-control input-datepicker pointer" data-date-format="yyyy-mm-dd" placeholder="To date" readonly />
					</div>
				</div>
				<div class="col-md-1">
					<button id="filter_data" href="#" class="btn btn-info btn-block">Filter</button>
				</div>
				<div class="col-md-1">
					<button id="reset_data" class="btn btn-warning btn-block">Reset</button>
				</div>
				<div class="col-md-2">
					<button id="refresh_table" class="btn btn-info">Refresh Table</button>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th width="20px">#</th>
						<th>id</th>
						<th>Place Name</th>
						<th>Create Date</th>
						<th>Total Link Clicked</th>
						<th>Total Open Playstore</th>
						<th>Total Open Appstore</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
