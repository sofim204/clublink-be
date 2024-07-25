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
		<li>Shipment</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Shipment</strong></h2>
		</div>
		<div class="row" >
			<div class="col-md-12" style="margin-bottom: 1em;">
				<div class="row"  style="margin-bottom: 1em;">
					<div class="col-md-3">
						<label for="ifdelivery_date">Delivery Date</label>
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input id="ifdelivery_date" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="Delivery date" />
						</div>
					</div>
					<div class="col-md-3">
						<label for="ifcourier_service">Courier Service</label>
						<select id="ifcourier_service" class="form-control">
							<option value="">- View All -</option>

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
					<div class="col-md-2">
						<label for="ifshipment_status">Shipment Status</label>
						<select id="ifshipment_status" class="form-control">
							<option value="">-- View All --</option>
							<option value="pending">Pending</option>
							<option value="not_yet_sent">Not yet sent</option>
							<option value="delivery_in_progress">Delivery in progress</option>
							<option value="delivered">Delivered</option>
							<option value="received">Received</option>
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
			</div>
		</div>

		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr>
						<th class="text-center">Order ID</th>
						<th class="text-center">Product ID</th>
						<th>Product Name</th>
						<th>Shipment</th>
						<th>Cost</th>
						<th>Distance</th>
						<th>Pickup Date</th>
						<th>Delivery Date</th>
						
						<!-- by Donny Dennison - 13 november 2020 11:14 -->
						<!-- change title thead in shipments menu -->
						<!-- <th>Received Date</th> -->
						<th>Buyer's Receipt Date</th>

						<th>Receipt Number</th>

						<!-- by Donny Dennison - 13 november 2020 11:14 -->
						<!-- change title thead in shipments menu -->
						<!-- <th>Status</th> -->
						<th>Shipment Status</th>

					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
</div>
