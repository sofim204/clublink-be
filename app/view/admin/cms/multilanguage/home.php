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
		<div class="row" >
			<div class="col-md-12">
				<div class="btn-group" style="display:flex; justify-content: center; align-items: center;">
					<a id="addnew" href="javascript:void(0)" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Multi Language</li>
	</ul>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Multi Language</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-2">
					<label for="fltype_multilanguage">Type Multilanguage</label>
					<select id="fltype_multilanguage" class="form-control">
						<option value="">-- View All --</option>
						<option value="api">Api</option>
						<option value="mobile">Mobile</option>
					</select>
				</div>
				<div class="col-md-1">
					<button id="reset-filter" class="btn btn-block btn-danger">Clear</button>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF">
						<th width="20px">No.</th>
						<th class="text-center">ID</th>
						<th>Variable Name</th>
						<th>Indonesia</th>
						<th>English</th>
						<th>Korea</th>
						<th>Thailand</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
