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
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group pull-left">
					<a id="btn_list_qrcode" href="<?=base_url_admin('misc/sellondownload/')?>" class="btn btn-back" style="margin-right: 10px;"><i class="fa fa-chevron-left"></i>  Back</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="btn_create_qrcode" href="javascript:void(0)" class="btn btn-info"><i class="fa fa-plus"></i> Create QR Code</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<li>Sellon QRCode</li>
	</ul>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Sellon QRCode</strong></h2>
		</div>
		<div class="block-section">
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th width="20px">#</th>
						<th>id</th>
						<th>Place Name</th>
						<th>QRCode</th>
						<th>URL</th>
						<th>Create Date</th>
						<th>Creator</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>
