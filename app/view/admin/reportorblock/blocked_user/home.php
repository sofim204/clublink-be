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

	.text-left {
		text-align: left;
	}

	.input-datepicker {
		cursor: pointer;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Blocked User</li>
	</ul>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Blocked User</strong></h2>
		</div>
		<div class="block-section">
			<div class="row">

			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th width="10px">No.</th>
						<th width="10px">ID</th>
						<th>User Name</th>
						<th>User Email</th>
						<th>Blocked User</th>
						<th>Blocked User Email</th> 
						<th>Block Date</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
