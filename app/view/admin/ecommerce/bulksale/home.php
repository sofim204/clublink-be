<style>
h4.tbl-content {
	margin: 0;
	line-height: 1;
}
.tbl-content-category {
	margin: 0.5em 0;
	color: #9c9c9c;
	font-weight: bold;
	line-height: 1;
}
.tbl-product-properties {
	margin-top: 0.5em;
}
.img-responsive.img-icon {
	max-width: 64px;
	border-radius: 10px;
	border: 1px #acacac solid;
	margin-left: 0.5em;
}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('ecommerce/bulksale/tambah/'); ?>" class="btn btn-info" style="display: none;"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Sell on me</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Sell on me</strong></h2>
		</div>
		<div class="row" style="margin-bottom: 1em;">
			<div class="col-md-3">
				<label for="ifcdate_min">From Create Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_min" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" />
				</div>
			</div>
			<div class="col-md-3">
				<label for="ifvdate_min">From Visit Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifvdate_min" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" />
				</div>
			</div>
			<div class="col-md-3">
				<label for="if_is_agent">Agent status</label>
				<select id="if_is_agent" class="form-control">
					<option value="">-- View All --</option>
					<option value="1">By Agent</option>
					<option value="0">Guest</option>
				</select>
			</div>
			<div class="col-md-3">
				<label for="areset_do">&nbsp;</label>
				<a id="areset_do" href="#" class="btn btn-warning btn-block"> Reset</a>
			</div>
		</div>
		<div class="row" style="margin-bottom: 1em;">
			<div class="col-md-3">
				<label for="ifcdate_max">To Create Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifcdate_max" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" />
				</div>
			</div>
			<div class="col-md-3">
				<label for="ifvdate_max">To Visit Date</label>
				<div class="input-group">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input id="ifvdate_max" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" />
				</div>
			</div>
			<div class="col-md-3">
				<label for="if_action_status">Visit status</label>
				<select id="if_action_status" class="form-control">
					<option value="">-- View All --</option>
					<option value="pending">Pending</option>
					<option value="visited">Visited</option>
					<option value="completed">Completed</option>
					<option value="rejected">Rejected</option>
				</select>
			</div>
			<div class="col-md-3">
				<label for="if_action">&nbsp;</label>
				<button id="if_action" class="btn btn-info btn-block">Filter</button>
			</div>
		</div>
		<div class="row"><div class="col-md-12"><br /></div></div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr>
						<th>ID</th>
						<th>Created</th>
						<th>Agent Status</th>
						<th>Description</th>
						<th>Address</th>
						<th>Status</th>
						<th>Visit Date</th>
						<th>Price</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>

	</div>
	<!-- END Content -->
</div>
