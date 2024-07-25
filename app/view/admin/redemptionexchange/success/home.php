<!-- by Muhammad Sofi 29 December 2021 15:00 | show x button in search box -->
<style>
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}
	table#drTable tr:hover {
		background-color: #EFBF65;
	}
	table#drTable tbody td {
		word-break: break-word;
		vertical-align: top;
	}

	/* change input datepicker readonly background color */
	#ifcdate_start, #ifcdate_end {
		background-color: #FFFFFF;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header" style="display: none">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				&nbsp;
			</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="atambah" href="#" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Redemption Exchange</li>
		<li>Success History</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Success History List</strong></h2>
		</div>
		<div class="row">
<!-- 			<div class="col-md-4">&nbsp;</div> -->
			<div class="col-md-2">
				<label for="ifcdate_min">From Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_start" type="date" class="form-control" placeholder="From date" />
				</div>
			</div>
			<div class="col-md-2">
				<label for="ifcdate_max">To Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_end" type="date" class="form-control" placeholder="To date" />
				</div>
			</div>
			<div class="col-md-3" style="display: none;">
				<label for="ifstatus">Status</label>
				<select id="ifstatus" class="form-control">
					<option value="">-- View All --</option>
					<option value="1">Wallet Balance Deducted</option>
					<option value="2">Top Up Problem</option>
					<option value="3">Wallet Balance Refunded</option>
				</select>
			</div>
			<div class="col-md-1">
				<label for="fl_button">&nbsp;</label>
				<button id="fl_button" type="button" class="btn btn-info btn-block"><i class="fa fa-filter"></i> Filter</button>
			</div>
			<div class="col-md-1">
				<label for="fl_button">&nbsp;</label>
				<button id="fl_reset" type="button" class="btn btn-warning btn-block">Reset</button>
			</div>		
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<button id="bdownload_xls_agent" class="btn btn-danger btn-block"><i class="fa fa-download"></i> Export Xls for Agent</button>
			</div>	

	<!-- Improve By Aditya Adi Prabowo 8/18/2020 14:14
    Add button to print Xls Detail Data User
    Start Improve -->
			<!-- <div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<button id="detail_xls" class="btn btn-success btn-block"><i class="fa fa-download"></i> Export Detail Xls</button>
			</div> -->
	<!-- End Improve -->
		</div>
		<br />
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">
	
    var dtToday = new Date();

    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
     day = '0' + day.toString();
    var maxDate = year + '-' + month + '-' + day;
    $('#ifcdate_start').attr('max', maxDate);
    $('#ifcdate_end').attr('max', maxDate);
</script>
