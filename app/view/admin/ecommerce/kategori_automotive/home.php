<style>
	table#drTable tr:hover {
		background-color: #EFBF65;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<?php if($user_role == "marketing" || $user_role == "customer_service") { ?>
						&nbsp;
					<?php } else { ?>
						<a id="" href="<?=base_url_admin('ecommerce/kategori_automotive/tambah/')?>" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Automotive Categories</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Product Automotive Categories</strong></h2>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th class="text-center" width="50px">No.</th>
						<th class="text-center">ID</th>
						<th>Icon</th>
						<th>Brand Name</th>
						<th>Priority</th>
						<th>Priority Indo</th>
						<th>Type</th>
						<th>Is Active?</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
</div>
