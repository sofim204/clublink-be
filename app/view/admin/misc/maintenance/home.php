<div id="page-content">
	<!-- Static Layout Header -->
	
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<li>Maintenance App</li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	
	<div class="row">
		<div class="col-md-6">

			<!-- by Muhammad Sofi 2 February 2022 19:12 | add maintenance setup -->
			<!-- start setup maintenancee -->
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/maintenance/')?>app_config" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C2" class="control-label">Maintenance App</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<select id="fs_app_config_remark_C2" name="app_config_remark_C2" class="form-control">
											<option value="on">ON</option>
											<option value="off">OFF</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-default"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- end block setup maintenance -->
			
		</div> <!-- end left -->
		
		<div class="col-md-6">
		</div><!-- end right -->
	</div>
	<!-- END Content -->
	
</div>
