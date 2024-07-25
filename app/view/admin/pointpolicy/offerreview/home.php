<div id="page-content">
	<!-- Static Layout Header -->
	
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<!-- <li>Setup</li> -->
		<li>Point Policy Offer Review</li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	
	<div class="row">
		<div class="col-md-5">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>For Seller (Meetup, Motorcycle, Car)</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/offerreview/')?>eq" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EQ" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EQ" name="leaderboard_point_remark_EQ" type="number" min="0" max="100" class="form-control" />
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
		</div> <!-- end left -->

		<div class="col-md-5">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>For Buyer (Meetup, Motorcycle, Car)</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/offerreview/')?>er" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_ER" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_ER" name="leaderboard_point_remark_ER" type="number" min="0" max="100" class="form-control" />
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
	</div>

	<div class="row">
		<div class="col-md-5">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>For Seller (Free)</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/offerreview/')?>es" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_ES" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_ES" name="leaderboard_point_remark_ES" type="number" min="0" max="100" class="form-control" />
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

		<div class="col-md-5">
			<!-- <div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>For Buyer (Free)</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/offerreview/')?>et" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_ET" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_ET" name="leaderboard_point_remark_ET" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div> -->
		</div>

	</div>
</div>
