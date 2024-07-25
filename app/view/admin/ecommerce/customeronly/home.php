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
		<li>E-Commerce</li>
		<li>Customer</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Customer</strong></h2>
		</div>
		<div class="row">
			<div class="col-md-2">
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
			</div>
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
						<th class="text-center" style="min-width: 35px;">ID</th>
						<th>Image</th>
						<th style="min-width: 90px;">Name</th>
						<th style="min-width: 90px;">Contact</th>
						<th>IP address</th>
						<th>Is Emulator</th>
						<th>Status</th>
						<th>Permanent Inactive?</th>
						<th>Recommender</th>
						<!-- Improve By Aditya Adi Prabowo 7/9/2020
							Add field device -->
						<th>Device</th>
						<th>Device ID</th>
						<!-- End Of Improve-->
						<!-- <th width="10px">FCM Token</th> -->
						<th style="min-width: 90px;">FCM Token</th>
						<th style="min-width: 120px">Address</th>
						<!-- START by Donny Dennison - 15 august 2022 13:16
							Add fb_id, google_id, apple_id, and email status in cms -->
						<th>Facebook</th>
						<th>Apple</th>
						<th>Google</th>
						<th>Email</th>
						<!-- END by Donny Dennison - 15 august 2022 13:16
							Add fb_id, google_id, apple_id, and email status in cms -->

						<!-- by Donny Dennison - 23 august 2022 12:11
            				Add phone status in cms -->
						<th>Phone</th>

					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div class="row" style="margin-top: 10px;">
			<div class="pull-right" style="margin-right: 15px;">
				<!-- <label for="fl_button">&nbsp;</label> -->
				<button id="b_permanent_acc_stop_mass" type="button" class="btn btn-info text-left"><i class="fa fa-check-circle"></i> Permanent Acc Stop</button>
			</div>
		</div>

	</div>
	<!-- END Content -->
</div>
