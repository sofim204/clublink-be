<style>
	/* refer to https://stackoverflow.com/questions/60149994/how-to-add-a-x-to-clear-input-field 
		by Muhammad Sofi 27 December 2021 18:00 | Add x button to clear search box 
	*/
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="addnew" href="javascript:void(0)" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Common Code</li>
	</ul>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Common Code</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-2">
					<label for="fltype_classified">Filter By</label>
					<select id="fltype_classified" class="form-control">
						<option value="">-- View All --</option>
						<option value="address">Address</option>
						<option value="setting_notification_buyer">Notification Buyer</option>
						<option value="setting_notification_seller">Notification Seller</option>
						<option value="setting_notification_user">Notification User</option>
						<option value="order_buyer_address">Order Buyer Address</option>
						<option value="order_seller_address">Order Seller Address</option>
						<option value="product_fee">Product Fee</option>
						<option value="app_config">App Config</option>
						<option value="product_report">Product Report</option>
						<option value="leaderboard_point">Leaderboard Point</option>
					</select>
				</div>
				<div class="col-md-1">
					<button id="reset-filter" class="btn btn-block btn-danger">Clear</button>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr>
						<th width="20px">No. </th>
						<th width="20px">ID</th>
						<th>Classified</th>
						<th>Code</th>
						<th>Codename</th>
						<th>Yes/No</th>
						<th>Remark</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
