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
		<li>Transaction by Seller</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Orders</strong></h2>
		</div>
		<div class="row" >
			<div class="col-md-12" style="margin-bottom: 1em;">
				<div class="row"  style="margin-bottom: 1em;">
					<div class="col-md-3">
						<label for="ifdate_order_min">From Order Date</label>
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input id="ifdate_order_min" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" />
						</div>
					</div>
					<div class="col-md-3">
						<label for="ifcourier_service">Courier Service</label>
						<select id="ifcourier_service" class="form-control">
							<option value="">-- View All --</option>

							<!-- by Donny Dennison - 15 september 2020 17:45
        					change name, image, etc from gogovan to gogox -->
							<!-- <option value="gogovan">Gogovan Next Day</option>
							<option value="gogovan_sameday">Gogovan Same Day</option> -->
							<option value="gogox">Gogox Next Day</option>
							<option value="gogox_sameday">Gogox Same Day</option>

							<option value="qxpress">QXpress Next Day</option>
							<option value="qxpress_sameday">QXpress Same Day</option>

							<!-- by Donny Dennison - 23 september 2020 15:42
							add direct delivery feature -->
							<option value="direct_delivery">Direct Delivery</option>
							
						</select>
					</div>
					<div class="col-md-3">
						<label for="ifseller_status">Seller Status</label>
						<select id="ifseller_status" class="form-control">
							<option value="">-- View All --</option>
							<option value="unconfirmed">Unconfirmed</option>
							<option value="confirmed">Confirmed</option>
							<option value="rejected">Rejected</option>
						</select>
					</div>
					<div class="col-md-3">
						<label for="areset_do">&nbsp;</label>
						<a id="areset_do" href="#" class="btn btn-warning btn-block"> Reset</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<label for="ifdate_order_max">To Order Date</label>
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input id="ifdate_order_max" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" />
						</div>
					</div>
					<div class="col-md-3">
						<label for="ifshipment_status">Shipment Status</label>
						<select id="ifshipment_status" class="form-control">
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
					<div class="col-md-3">
						<label for="iforder_status">Order Status</label>
						<select id="iforder_status" class="form-control">
							<option value="">-- View All --</option>
							<option value="forward_to_seller">Forward to Seller</option>
							<option value="cancelled">Cancelled / Expired</option>
						</select>
					</div>
<!-- 					<div class="col-md-3">&nbsp;</div> -->
					<div class="col-md-3">
<!-- 						<label for="afilter_do">&nbsp;</label> -->
						<a id="afilter_do" href="#" class="btn btn-info btn-block"> Filter</a>
					</div>
				</div>
			</div>
		</div>

		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr>
						<th class="text-center">ID</th>
						<th>Order Date</th>
						<th>Product Name</th>
						<th>Total Product Price</th>
						<th>Seller Name</th>
						<th>Seller Location</th>
						<th>Buyer Name</th>
						<th>Buyer Location</th>
						<th>Order Status</th>
						<th>Courier Service</th>
						<th>Seller Status</th>
						<th>Buyer Status</th>
						<th>Shipment Status</th>
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
