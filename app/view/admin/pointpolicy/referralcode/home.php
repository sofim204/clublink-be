<div id="page-content">
	<!-- Static Layout Header -->
	
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<!-- <li>Setup</li> -->
		<li>Point Policy Referral Code</li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	
	<div class="row">
		<div class="col-md-4">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>Sign Up With Referral</strong><h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/referralcode/')?>referralcode_recommendee_signup_with_referral" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_EY" class="control-label">Recommendee</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EY" name="leaderboard_point_remark_EY" type="number" min="0" max="1000" maxlength="3" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save" aria-hidden="true"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
						<form action="<?=base_url('api_admin/pointpolicy/referralcode/')?>referralcode_recommender_signup_with_referral" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_EZ" class="control-label">Recommender</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EZ" name="leaderboard_point_remark_EZ" type="number" min="0" max="1000" maxlength="3" class="form-control" />
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
		<div class="col-md-4">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>Sign Up Without Referral</strong><h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/referralcode/')?>referralcode_recomendee_signup_without_referral" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E4" class="control-label">Recommendee</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E4" name="leaderboard_point_remark_E4" type="number" min="0" max="1000" maxlength="3" class="form-control" />
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
		</div><!-- end right -->
	</div>

	<div class="row">
		<div class="col-md-4">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>Input Referral Manually</strong><h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/referralcode/')?>referralcode_recommendee_input_referral_manually" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-6">
									<label for="fs_leaderboard_point_remark_E5" class="control-label">Recommendee</label>
								</div>
								<div class="col-md-6">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E5" name="leaderboard_point_remark_E5" type="number" min="0" max="1000" maxlength="3" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save" aria-hidden="true"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
						<form action="<?=base_url('api_admin/pointpolicy/referralcode/')?>referralcode_recommender_input_referral_manually" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-6">
									<label for="fs_leaderboard_point_remark_E6" class="control-label">Recommender</label>
								</div>
								<div class="col-md-6">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E6" name="leaderboard_point_remark_E6" type="number" min="0" max="1000" maxlength="3" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save" aria-hidden="true"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
						<form action="<?=base_url('api_admin/pointpolicy/referralcode/')?>referralcode_recommender_input_referral_manually_deadline_day" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-6">
									<label for="fs_leaderboard_point_remark_E7" class="control-label">Deadline/Day</label>
								</div>
								<div class="col-md-6">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E7" name="leaderboard_point_remark_E7" type="number" min="0" max="1000" maxlength="3" class="form-control" />
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