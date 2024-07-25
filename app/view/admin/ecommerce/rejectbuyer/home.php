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
		<li>Rejected item(s) by Buyer</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Rejected item(s) by Buyer</strong></h2>
		</div>
		<div class="row">
			<div class="col-md-12">&nbsp;</div>
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
			<div class="col-md-5">&nbsp;</div>
			<div class="col-md-2">
				<label class="if_action">&nbsp;</label>
				<button id="if_reset" class="btn btn-warning btn-block">Reset Filter</button>
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
			<div class="col-md-3">
				<label class="if_settlement_status">Resolution</label>
				<select id="if_settlement_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="wait">Unconfirmed</option>
					<option value="paid_to_buyer">Paid to Buyer</option>
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
			<form id="fupdate-status-payment" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" onsubmit="return false;">
				<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
					<thead>
						<tr>
							<th class="text-center">ID</th>
							<th>Order Date</th>
							<th>Invoice Number</th>
							<th>Product</th>
							<th>Price</th>
							<th>Qty</th>
							<th>Sub Total</th>
							<th>Resolution</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</form>
		</div>

	</div>
	<!-- END Content -->
</div>
