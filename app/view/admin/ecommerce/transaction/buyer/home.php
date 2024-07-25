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
		<li>Transaction by Buyer</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Orders</strong></h2>
		</div>
		<div class="row" style="margin-bottom:16px;">
			<div class="col-md-3">
				<label for="ifcdate_min">From Order Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" />
				</div>
			</div>
			<div class="col-md-2">
				<label for="if_payment_gateway">Payment Method</label>
				<select id="if_payment_gateway" class="form-control">
					<option value="">-- View All --</option>
					<option value="2c2p">2C2P</option>
				</select>
			</div>
		</div>
		<div class="row" style="margin-bottom:16px;">
			<div class="col-md-3">
				<label for="ifcdate_max">To Order Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_end" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" />
				</div>
			</div>
			<div class="col-md-2">
				<label class="if_payment_status">Payment Status</label>
				<select id="if_payment_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="paid">Paid</option>
					<option value="pending">Wait (Unpaid)</option>
				</select>
			</div>

			<div class="col-md-3">
				<label class="if_order_status">Order Status</label>
				<select id="if_order_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="waiting_for_payment">Waiting for Payment</option>
					<option value="forward_to_seller">Forward to Seller</option>
					<option value="completed">Completed</option>
					<option value="cancelled">Cancelled</option>
				</select>
			</div>

			<div class="col-md-2">
				<label for="areset_do">&nbsp;</label>
				<a id="areset_do" href="#" class="btn btn-warning btn-block"> Reset</a>
			</div>
			<div class="col-md-2">
				<label for="afilter_do">&nbsp;</label>
				<a id="afilter_do" href="#" class="btn btn-info btn-block"> Filter</a>
			</div>
		</div>

		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr>
						<th class="text-center">ID</th>
						<th>Invoice</th>
						<th>Order Date</th>
						<th>Buyer Name</th>
						<th>Buyer Location</th>
						<th>Payment Method</th>
						<th>Item Total</th>
						<th>Sub Total</th>
						<th>Shipping Cost</th>
						<th>Grand Total</th>
						<th>Payment Status</th>
						<th>PG Cost *</th>
						<th>Order Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<small>*) VAT included</small>
	</div>
	<!-- END Content -->
</div>
