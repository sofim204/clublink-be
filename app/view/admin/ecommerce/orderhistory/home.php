<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="atambah" href="#" class="btn btn-info" style="display:none;"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Transaction History</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Transaction History</strong></h2>
		</div>
		<div class="row">

			<div class="col-md-7">&nbsp;</div>
		</div>
		<div class="row" style="display:none;">
			<div class="col-md-12" style="background-color: #f5f5f5; padding: 1em; margin-bottom: 1em;">

				<div class="row">
					<div class="col-md-12">
						<h4><i class="fa fa-filter"> Filter</i></h4>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-users"></i>
							</div>
							<input id="filter_b_user_id" type="text" class="form-control" placeholder="CustID" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input id="min" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="Dari Tgl" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input id="max" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="Ke Tgl" />
						</div>
					</div>
					<div class="col-md-3">
						<div class="btn-group">
							<a id="adownload_do" href="#" class="btn btn-success"><i class="fa fa-download"></i> Check Stock</a>
							<a id="afilter_do" href="#" class="btn btn-info"> Filter</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row" style="margin-bottom:16px;">
			<div class="col-md-5">&nbsp;</div>

			<div class="col-md-2">
				<button id="bdownload_xls" class="btn btn-success btn-block">Export to Excel</button>
			</div>
			<div class="col-md-2">
				<select id="if_order_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="pending">Pending</option>
					<option value="wait_for_payment">Wait for Payment</option>
					<option value="forward_to_seller">Forward to Seller</option>
				</select>
			</div>
			<div class="col-md-2">
				<select id="if_payment_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="paid">Paid</option>
					<option value="unpaid">Unpaid</option>
					<option value="refund">Refund</option>
					<option value="pending">Pending</option>
				</select>
			</div>
			<div class="col-md-1">
				<button id="if_action" class="btn btn-info btn-block">Filter</button>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr>
						<th class="text-center">ID</th>
						<th>Order Date</th>
						<th>Invoice Number</th>
						<th>Product Name</th>
						<th>Product Price</th>
						<th>Paid to Seller</th>
						<th>Return to Buyer</th>
						<th>Payment Cost</th>
						<th>Seller Name</th>
						<th>Cancellation Fee</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>

	</div>
	<!-- END Content -->
</div>
