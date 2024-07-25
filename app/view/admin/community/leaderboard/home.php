<style>
	table {
		width: 100%;
	}

	th, td {
		padding: 8px;
		text-align: left;
		border-bottom: 1px solid #ddd;
	}

	table#drTable tr:hover {
		background-color: #EFBF65;
	}

	/* refer to https://stackoverflow.com/questions/60149994/how-to-add-a-x-to-clear-input-field 
		by Muhammad Sofi 27 December 2021 18:00 | Add x button to clear search box 
	*/
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}
</style>
<div id="page-content">
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('community/leaderboard/leaderboard_history/')?>" class="btn btn-info btn-block">
						<i class="fa fa-star-half-o"></i> Leaderboard Point History
					</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>Leaderboard</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong>Filter</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-3">
					<label for="select_general_location">General Location</label>
					<select id="select_general_location" class="form-control"></select>
				</div>
				<div class="col-md-1">
					<button id="reset-filter" class="btn btn-block btn-danger">Clear</button>
				</div> 
			</div>
		</div>
	</div>
	<div class="block full">
		<div class="block-title">
			<!-- <h2 id="text_gl"><strong>Leaderboard on Your Area</strong></h2> -->
			<h2 id="text_gl"><strong>Leaderboard</strong></h2>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" style="width:100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th width="50px">Ranking</th>
						<th width="50px">ID</th>
						<th>User Image</th>
						<th width="200px">Name</th>
						<th>Total Post</th>
						<th>Total Points</th>
						<th>General Location</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
</div>