<style>
	table#drTable tr:hover {
		background-color: #EFBF65;
	}
	table.total-section {
		background-color: #DDD1C8;
		margin: auto;
		font-size: 1.3rem;
		text-align: right;
		border-radius: 4px;
	}
	table.total-section > tr > th, td {
		padding: 8px;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<!-- <div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="atambah" href="#" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div> -->
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Visitor Count</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Visitor Count</strong></h2>
		</div>
		<div class="row">
			<div class="col-md-12">&nbsp;</div>
		</div>

		<div class="row" style="margin-bottom:16px;">
			<div class="col-md-3">
				<label for="ifcdate_start">From Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" />
				</div>
			</div>
			<div class="col-md-5">&nbsp;</div>
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<button id="if_reset" class="btn btn-warning btn-block">Reset Filter</button>
			</div>
			<div class="col-md-2">
				<?php if($user_role == "marketing" || $user_role == "customer_service") { ?>
					&nbsp;
				<?php } else { ?>
					<label class="if_action">&nbsp;</label>
					<button id="btn_udid_account" class="btn btn-info btn-block">UDID & Account</button>
				<?php } ?>
			</div>
		</div>
		<div class="row" style="margin-bottom:16px;">
			<div class="col-md-3">
				<label for="ifcdate_end">To Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_end" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" />
				</div>
			</div>
			<div class="col-md-3">
				<label class="mobile_type">Mobile Type</label>
				<select id="if_mobile_type" class="form-control">
					<option value="">-- View All --</option>
					<option value="android">Android</option>
					<option value="ios">iOS</option>
				</select>
			</div>
			<div class="col-md-2">&nbsp;</div>
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<button id="if_action" class="btn btn-info btn-block">Filter</button>
			</div>
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<button id="bdownload_xls" class="btn btn-success btn-block">Export to Excel</button>
			</div>
		</div>

		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th class="text-center" style="width: 20px;">No.</th>
						<th class="text-center" style="width: 20px;">ID</th>
						<th>Mobile Type</th>
						<th>Total Visit</th>
						<!-- <th>Daily Visitor Count</th> -->
						<th>Sign Up / Login</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="row" style="margin-bottom:16px;"></div>
		<table class="total-section">
			<tr>
				<th class="col-md-2 col-lg-8">Total Android</th>
				<td class="col-md-1">=</td>
				<td class="col-md-4"><label><span id ='totalAndroid'>0</span></td>
			</tr>
			<tr>
				<th class="col-md-2 col-lg-8">Total IOS</th>
				<td class="col-md-1">=</td>
				<td class="col-md-4"><label><span id ='totalIOS'>0</span></td>
			</tr>
			<tr>
				<th class="col-md-2 col-lg-8">Total ALL</th>
				<td class="col-md-1">=</td>
				<td class="col-md-4"><label><span id ='totalAll'>0</span></td>
			</tr>
		</table>
	</div>
	<!-- END Content -->
</div>
