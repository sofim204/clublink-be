<style>
	table#drTable tr:hover {
		background-color: #EFBF65;
	}
</style>
<div id="page-content">
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>CRM</li>
		<li>Discuss</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong><i class="fa fa-wechat"></i>&nbsp;Q&A Product</strong></h2>
		</div>
		<div class="row">
			<div class="col-md-3">
				<label for="bfilter">&nbsp;</label>
				<button id="bfilter" type="button" class="btn btn-danger btn-block"><i class="fa fa-wechat"></i> Reported Discussion : <?=$count->jumlah ?></button>
				<br>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th class="text-center">Q&A ID</th>
						<th>Product</th>
						<th>User</th>
						<th>From</th> 
						<th>Date</th>
						<th>Status</th> 
						<th>Question</th>
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
