<div id="page-content">
	<!-- Static Layout Header -->
	
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<!-- <li>Setup</li> -->
		<li>Point Policy Buy and Sell</li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	
	<div class="row">
		<div class="col-md-5">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>Product Creation</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/buyandsell/')?>product_creation" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EA" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EA" name="leaderboard_point_remark_EA" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
						<!-- <form action="<?=base_url('api_admin/pointpolicy/buyandsell/')?>product_creation_perday" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EM" class="control-label">Point (given a day)</label>
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
						</form> -->
						<form action="<?=base_url('api_admin/pointpolicy/buyandsell/')?>product_creation_daily_limit" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_E10" class="control-label">Limit</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E10" name="leaderboard_point_remark_E10" type="number" min="0" max="100" class="form-control" />
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
						<label for="" class="control-label"><h4><strong>Product Q&A Reply</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/buyandsell/')?>product_qa_reply" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EB" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EB" name="leaderboard_point_remark_EB" type="number" min="0" max="100" class="form-control" />
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
						<label for="" class="control-label"><h4><strong>Product Share</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/buyandsell/')?>product_share_total" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EC" class="control-label">Total</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EC" name="leaderboard_point_remark_EC" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>	
						<form action="<?=base_url('api_admin/pointpolicy/buyandsell/')?>product_share" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_ED" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_ED" name="leaderboard_point_remark_ED" type="number" min="0" max="100" class="form-control" />
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
						<label for="" class="control-label"><h4><strong>Product Upload Video</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/pointpolicy/buyandsell/')?>product_upload_video" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_EO" class="control-label">Point</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_EO" name="leaderboard_point_remark_EO" type="number" min="0" max="100" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
						<form action="<?=base_url('api_admin/pointpolicy/buyandsell/')?>product_upload_video_daily_limit" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_leaderboard_point_remark_E8" class="control-label">Limit</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E8" name="leaderboard_point_remark_E8" type="number" min="0" max="100" class="form-control" />
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
</div>
