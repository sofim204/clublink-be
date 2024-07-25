<div id="page-content">
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em; display:none">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('community/discussion/reported/')?>" class="btn btn-danger btn-block">
						<i class="fa fa-wechat"></i> Reported Post : <?=$count->jumlah ?>
					</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>Like</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Community Like</strong></h2>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr>
						<th class="text-center">ID</th>
						<th>Type</th>
						<th>Emoji</th>
						<th>User</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>

	</div>
	<!-- END Content -->
</div>
