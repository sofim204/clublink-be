<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Payment</li>
		<li>Current Bank: <?=$app_bank->nama?></li>
	</ul>
	<!-- END Static Layout Header -->

	<?php if($app_bank->nama == '-'){ ?>
	<div class="alert alert-info">
		<p><b>Caution</b> Please setup the <a href="<?=base_url_admin("misc/setup/")?>" target="_blank" style="text-decoration: underline;">application bank account</a> first before doing payment process!</p>
	</div>
	<?php } ?>

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Payment List</strong></h2>
		</div>
		<div class="row">

			<div class="col-md-7">&nbsp;</div>
		</div>
		<div class="row" style="margin-bottom:16px;">
			<div class="col-md-3">
				<label for="ifcdate_start">From Order Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" />
				</div>
			</div>
			<div class="col-md-2">
				<label class="if_seller_status">Seller Status</label>
				<select id="if_seller_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="confirmed">Confirmed</option>
					<option value="rejected">Rejected</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<button id="bpg_xls" class="btn btn-success btn-block" title="Payment Gateway Report"><i class="fa fa-download"></i> PG Report(s)</button>
			</div>
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<!-- by Muhammad Sofi 21 December 2021 14:30 | fix error while click button Seller Payment(s) -->
				<button id="bdownload_xls" class="btn btn-success btn-block"><i class="fa fa-download"></i> Seller Payment(s)</button>
			</div>

			<!-- by Donny Dennison - 19 January 2020 11:17 -->
			<!-- add seller settlement download excel -->
			<div class="col-md-3">
				<label class="if_action">&nbsp;</label>
				<button id="sellerSettlementDownload_xls" class="btn btn-success btn-block"><i class="fa fa-download"></i> Seller Settlement(s)</button>
			</div>
			
		</div>
		<div class="row" style="margin-bottom:16px;">
			<div class="col-md-3">
				<label for="ifcdate_end">To Order Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_end" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" />
				</div>
			</div>
			<div class="col-md-2">
				<label class="if_buyer_confirmed">Buyer Confirmed</label>
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
					<option value="completed">Paid</option>
				</select>
			</div>
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<button id="if_action" class="btn btn-info btn-block">Filter</button>
			</div>
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<button id="if_reset" class="btn btn-warning btn-block">Reset Filter</button>
			</div>
		</div>
		<div class="table-responsive">
			<form id="fupdate-status-settlement" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" onsubmit="return false;">
				<table id="drTable" class="table table-vcenter table-condensed table-bordered">
					<thead>
						<tr>
							<th><input name="select_all" value="1" id="drTable-select-all" type="checkbox" /></th>
							<th>Order Date</th>
							<th>Invoice Number</th>
							<th>Product</th>
							<th>Item Count</th>
							<th>Total Qty</th>
							<th>Sub Total</th>
							<th>Shipment Cost</th>
							<th>Profit</th>
							<th>Seller Earning</th>
							<th>Refund Amount</th>
							<th>Seller Status</th>
							<th>Buyer Confirmed</th>
							<th>Settlement Status</th>
							<th>Detail ID</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</form>
		</div>
		<hr>
		<p><button id="btn-payment-submit" type="button" class="btn btn-warning">Paid Selected (<span id="payment-selected-count">0</span>)</button></p>
	</div>
	<!-- END Content -->
</div>
