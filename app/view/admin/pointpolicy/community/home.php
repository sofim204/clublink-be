<div id="page-content">
	<!-- Static Layout Header -->
	
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<!-- <li>Setup</li> -->
		<li>Point Policy Community</li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	
	<div class="row">
		<div class="col-md-5">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>Community Creation</strong><h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_creation_total" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EE" class="control-label">Total</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EE" name="leaderboard_point_remark_EE" type="number" min="0" max="100" maxlength="3" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_creation_first_time" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EF" class="control-label">Point (first time)</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EF" name="leaderboard_point_remark_EF" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_creation" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EG" class="control-label">Point (text)</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EG" name="leaderboard_point_remark_EG" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
						<!-- <form action="<?=base_url('api_admin/pointpolicy/community/')?>community_creation_perday" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EL" class="control-label">Point (given a day)</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EL" name="leaderboard_point_remark_EL" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form> -->
					</div>
				</div>
			</div>
		</div> <!-- end left -->
		<div class="col-md-5">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>Community Reply</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_reply_total" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EH" class="control-label">Total</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EH" name="leaderboard_point_remark_EH" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_reply" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EI" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EI" name="leaderboard_point_remark_EI" type="number" min="0" max="100" class="form-control" />
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
						<label for="" class="control-label"><h4><strong>Community Like</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_like_total" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EJ" class="control-label">Total</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EJ" name="leaderboard_point_remark_EJ" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_like" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EK" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EK" name="leaderboard_point_remark_EK" type="number" min="0" max="100" class="form-control" />
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
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>Community Share</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_share_total" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EW" class="control-label">Total</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EW" name="leaderboard_point_remark_EW" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_share" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EX" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EX" name="leaderboard_point_remark_EX" type="number" min="0" max="100" class="form-control" />
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
						<label for="" class="control-label"><h4><strong>Community Upload Video</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_upload_video" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EP" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EP" name="leaderboard_point_remark_EP" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_upload_video_daily_limit" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_E9" class="control-label">Limit</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E9" name="leaderboard_point_remark_E9" type="number" min="0" max="100" class="form-control" />
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
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>Community Group Chat Participant</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_group_chat_more_than_equal" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EM" class="control-label">More Than Equal</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EM" name="leaderboard_point_remark_EM" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_group_chat_get_point" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EN" class="control-label">Get Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EN" name="leaderboard_point_remark_EN" type="number" min="0" max="100" class="form-control" />
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
						<label for="" class="control-label"><h4><strong>Community Upload Image</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/community/')?>community_upload_image" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_E13" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E13" name="leaderboard_point_remark_E13" type="number" min="0" max="100" class="form-control" />
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

	<!-- END Content -->
</div>
