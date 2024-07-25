<style>
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
	<!-- Static Layout Header -->
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Banner Carousel</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong>Banner Carousel</strong></h2>
		</div>
		<div class="row">
			<div class="col-md-5">&nbsp;</div>
			<div class="col-md-3">
				<label for="fl_is_active">Status</label>
				<select id="fl_is_active" class="form-control">
					<option value="">--view all--</option>
					<option value="1">Active</option>
					<option value="0">Inactive</option>
				</select>
			</div>
			<div class="col-md-2">
				<label for="fl_reset">&nbsp;</label>
				<button id="fl_reset" type="button" class="btn btn-warning btn-block">Reset</button>
			</div>
			<div class="col-md-2">
				<label for="addnewbanner">&nbsp;</label>
				<button id="addnewbanner" type="button" class="btn btn-info btn-block"><i class="fa fa-plus"></i> New</button>
			</div>
		</div>
		<br />
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" style="width: 100%;">
				<thead>
					<tr>
						<th width="20px">No.</th>
						<th class="text-center">ID</th>
						<th width="50px">Priority</th>
						<th>Image Banner</th>
						<th>Type Language</th>
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
