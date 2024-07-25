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
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<!-- <div class="col-md-6">&nbsp;</div> -->
			<div class="col-md-6">
				<div class="btn-group">
					<a id="" href="<?=base_url_admin('community/listing/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
			<!-- <div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('community/discussion/reported/')?>" class="btn btn-danger btn-block">
						<i class="fa fa-wechat"></i> Reported Discussion
					</a>
				</div>
			</div> -->
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>Discussion</li>
		<li>Reported</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<h2><strong>Reported Community Discussion</strong></h2>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
				<thead>
					<tr style="background-color: #FFFFFF;">
						<th class="text-center">#</th>
						<th class="text-center">Discussion ID</th>
						<th class="text-center">Submit Date</th>
						<th>Text</th>
						<!-- <th>Tipe</th> -->
						<th width="200px">User</th>
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
