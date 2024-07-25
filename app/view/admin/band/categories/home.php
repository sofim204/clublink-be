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
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div style="margin-left: 12px;" class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('band/categories/tambah/?add=category')?>" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('band/categories/tambah/?add=sub_category')?>" class="btn btn-info"><i class="fa fa-plus"></i> New Sub Category</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Band</li>
		<li>Categories</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong>Categories List</strong></h2>
		</div>
		<div class="row" style="margin-bottom: 20px;">
			<div class="col-md-2">
				<label for="fl_type">Type</label>
				<select id="fl_type" class="form-control">
					<option value="">--view all--</option>
					<option value="category">Category</option>
					<option value="sub category">Sub Category</option>
				</select>
			</div>
			<div class="col-md-2">
				<label for="fl_reset">&nbsp;</label>
				<button id="fl_reset" type="button" class="btn btn-warning btn-block">Reset</button>
			</div>
			<div class="col-md-2">
				<label for="fl_button">&nbsp;</label>
				<button id="fl_button" type="button" class="btn btn-info btn-block"><i class="fa fa-filter"></i> Filter</button>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th class="text-center" width="50px">No.</th>
						<th class="text-center">ID</th>
						<th>Icon</th>
						<th>Type</th>
						<th>Name(English)</th>
						<th>Name(Indonesia)</th>						
						<th>Prioritas</th>
						<th>Prioritas Indonesia</th>
						<th>Visible</th>
						<th>Date</th>
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