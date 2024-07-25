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

		<div class="row" style="margin-bottom:16px;">
			<div class="col-md-2">
				<label for="ifcdate_min">From Order Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" />
				</div>
			</div>
			<div class="col-md-2">
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
				<label class="if_payment_status">Payment Status</label>
				<select id="if_payment_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="paid">Paid</option>
					<option value="wait">Wait (Unpaid)</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="if_seller_status">Seller Status</label>
				<select id="if_seller_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="unconfirmed">Unconfirmed</option>
					<option value="confirmed">Confirmed</option>
					<option value="rejected">Rejected</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<button id="if_reset" class="btn btn-warning btn-block">Reset Filter</button>
			</div>
		</div>
		<div class="row" style="margin-bottom:16px;">
			<div class="col-md-2">
				<label for="ifcdate_max">To Order Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_end" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" />
				</div>
			</div>
			<div class="col-md-2">
				<label class="">Shipment Status</label>
				<select id="if_shipment_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="pending">Pending</option>
					<option value="not_yet_sent">Not yet sent</option>
					<option value="delivery_in_progress">Delivery in progress</option>

					<!-- By Donny Dennison - 08-07-2020 16:16
                	Request by Mr Jackie, add new shipment status "courier fail" -->
					<option value="courier_fail">Courier Fail</option>

					<option value="delivered">Delivered</option>
					<option value="received">Received</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="if_buyer_confirmed">Buyer Status</label>
				<select id="if_buyer_confirmed" class="form-control">
					<option value="">-- View All --</option>
					<option value="unconfirmed">Unconfirmed</option>
					<option value="confirmed">Confirmed</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="if_settlement_status">Settlement Status</label>
				<select id="if_settlement_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="unconfirmed">Unconfirmed</option>
					<option value="process">In Process</option>
					<option value="completed">Completed</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
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
						<th>Product</th>
						<th>Product Price</th>
						<th>Qty</th>
						<th>Sub Total</th>
						<th>Shipping Cost</th>
						<th>Profit</th>
						<th>Seller Earning</th>
						<th>Refund Amount</th>
						<th>Bank Trf Cost</th>
						<th>Order Status</th>
						<th>Payment Status</th>
						<th>Seller Status</th>
						<th>Shipment Status</th>
						<th>Buyer Status</th>
						<th>Settlement Status</th>
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
