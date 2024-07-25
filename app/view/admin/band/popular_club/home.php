<style>
	/* refer to https://stackoverflow.com/questions/60149994/how-to-add-a-x-to-clear-input-field 
		by Muhammad Sofi 27 December 2021 18:00 | Add x button to clear search box 
	*/
	.dataTables_wrapper .dataTables_filter input::-webkit-search-cancel-button {
		-webkit-appearance: button !important;
		padding: 2px;
		margin-right: 5px;
	}
	table#drTable tr:hover {
		background-color: #EFBF65;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<!-- <div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<button id="button_change_and_reorder_popular_club" class="btn btn-info" type="button" style="display: none;"><i class="fa fa-first-order"></i> Change All & Reorder Popular Club</button>
				</div>
			</div>
		</div> -->
		<!-- <div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<button id="button_add_to_popular_club_homepage" class="btn btn-info" type="button"><i class="fa fa-first-order"></i> Add Popular Club To Homepage</button>
				</div>
			</div>
		</div> -->
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Club</li>
		<li>Popular</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong>Popular Club (Home)</strong></h2>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th class="text-center" width="50px">No.</th>
						<th class="text-center">ID</th>
						<th>Club Name</th>
						<th>Status</th>
						<th>Priority</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Creator</th>
						<th>Create Date</th>
						<th>Club Id</th>
						<th>is active</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<!-- END Content -->
	<div class="container_change_and_reorder_popular_club">
		
	</div>
</div>