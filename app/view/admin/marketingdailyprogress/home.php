<!-- by Muhammad Sofi 29 December 2021 15:00 | show x button in search box -->
<style>
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}
	/* table#drTable tr:hover {
		background-color: #EFBF65;
	}
	table#drTable tbody td {
		word-break: break-word;
		vertical-align: top;
	} */
	tr:hover {background-color: #e3d1af;}
	tr.selected  {
		background-color: #EFBF65;
		/* color: #ffffff; */
	}
	.swal2-popup {
		font-size: 1.6rem !important;
		font-family: Georgia, serif;
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
		<li>Marketing Daily Progress</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Marketing Daily Progress</strong></h2>
		</div>
		<div class="row">
        	<div class="col-md-2">
                <label for="from_date">From Date</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input id="from_date" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" value="<?= $from_date; ?>" placeholder="From date" autocomplete="off"/>
                </div>
            </div>
            <div class="col-md-2">
                <label for="to_date">To Date</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input id="to_date" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" value="<?= $to_date; ?>" placeholder="To date" autocomplete="off"/>
                </div>
            </div>
			<!-- <div class="col-md-2">
				<label for="fl_is_confirmed">Email Status</label>
				<select id="fl_is_confirmed" class="form-control">
					<option value="">--view all--</option>
					<option value="1">Verified</option>
					<option value="0">Unverified</option>
				</select>
			</div>
			<div class="col-md-2">
				<label for="fl_pelanggan_status">Status</label>
				<select id="fl_pelanggan_status" class="form-control">
					<option value="">--view all--</option>
					<option value="active">Active</option>
					<option value="inactive">Inactive</option>
				</select>
			</div> -->
			<div class="col-md-2">
				<label for="fl_button">&nbsp;</label>
				<button id="fl_reset" type="button" class="btn btn-warning btn-block">Reset</button>
			</div>
			<div class="col-md-2">
				<label for="fl_button">&nbsp;</label>
				<button id="fl_button" type="button" class="btn btn-info btn-block"><i class="fa fa-filter"></i> Filter</button>
			</div>
		</div>
		<br />
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th>DATE</th>
						<th>Mobile Type</th>
						<th>(new)</th>
						<th>Total Visit</th>
						<th>(new)</th>
						<th>Sign Up / Login</th>
						<th>(new)</th>
						<th>TOTAL USERS</th>
						<th>(new)</th>
						<th>TOTAL LIST(COMMUNITY)</th>
						<th>(new)</th>
						<th>TOTAL LIST(ITEM)</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
</div>
