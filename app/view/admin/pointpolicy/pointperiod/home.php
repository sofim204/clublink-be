<div id="page-content">
	<!-- Static Layout Header -->
	
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<!-- <li>Setup</li> -->
		<li>Point Policy Point Period</li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	
	<div class="row">
		<div class="col-md-4">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>Point Period</strong><h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/pointperiod/')?>point_period" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EL" class="control-label">Day</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EL" name="leaderboard_point_remark_EL" type="number" min="0" max="100" maxlength="3" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save" aria-hidden="true"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
					</div>
				</div>
			</div>
		</div> <!-- end left -->
		<div class="col-md-6">
		</div><!-- end right -->
	</div>

	<!-- END Content -->
</div>
