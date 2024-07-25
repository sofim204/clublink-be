<!-- by Muhammad Sofi 13 January 2022 16:11 | remodel on sponsored menu -->
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

	.modal {
		overflow-y: auto;
	}

	.center-image {
		display:flex; 
		justify-content: center; 
		align-items: center;
	}

</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="atambah" href="#" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>First Login Image</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>First Login Image</strong></h2>
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
				<label for="fl_button">&nbsp;</label>
				<button id="fl_button" type="button" class="btn btn-info btn-block"><i class="fa fa-filter"></i> Filter</button>
			</div>
		</div>
		<br />
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th width="20px">No.</th>
						<th class="text-center">ID</th>
						<th width="50px">Priority</th>
						<th>Picture</th>
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
