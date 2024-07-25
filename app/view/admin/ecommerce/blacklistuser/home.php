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

	.btn-back {
        width: 85px;
        cursor: pointer;
        background: #F9F5F5;
        border: 1px solid #999;
        outline: none;
		color: #222121;
        transition: .3s ease;
    }

    .btn-back:hover {
        transition: .3s ease;
        background: #DD8A0D;
        border: 1px solid transparent;
        color:#FFF;
    }

	.text-left {
		text-align: left !important;
	}

</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-4">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin('ecommerce/pelanggan/')?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
			<div class="col-md-8">
				<div class="btn-group pull-right">
					<button id="add_data" class="btn btn-info"><i class="fa fa-plus"></i> New</button>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Ecommerce</li>
		<li>Blacklisted User</li>
		<li><span style="display: none;" id="admin_name"><?=$admin_name?></span></li>
	</ul>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Blacklisted User</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-2">
					<label for="ifiltertype">Type</label>
					<select id="ifiltertype" class="form-control">
						<option value="">-- View All --</option>
						<option value="fcm_token">FCM Token</option>
						<option value="ip_address">IP Address</option>
					</select>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th width="20px">No.</th>
						<th width="20px">ID</th>
						<th>Type</th>
						<th>Text</th>
						<th>Admin Name</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>