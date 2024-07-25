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
	<!-- <div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('game/ticket_shop/tambah/')?>" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div> -->
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Point Policy</li>
		<li>Convert / Mining SPT</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="row">
		<div class="col-md-6">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/convertspt/')?>minimum_transfer_to_bbt" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_app_config_remark_C19" class="control-label">Minimum Transfer SPT to BBT</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_app_config_remark_C19" name="app_config_remark_C19" type="number" value="<?=$fs_app_config_remark_C19?>" class="form-control">
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/convertspt/')?>seribuspt_equals_satuppt" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_app_config_remark_C20" class="control-label">X SPT equals 1 BBT</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_app_config_remark_C20" name="app_config_remark_C20" type="number" value="<?=$fs_app_config_remark_C20?>" class="form-control">
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>			
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/convertspt/')?>commission" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_app_config_remark_C21" class="control-label">Commission (%)</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_app_config_remark_C21" name="app_config_remark_C21" type="number" value="<?=$fs_app_config_remark_C21?>" class="form-control">
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
		</div>
	</div>
	<!-- END Content -->
</div>
