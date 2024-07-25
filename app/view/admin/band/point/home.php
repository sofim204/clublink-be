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
		<li>Band</li>
		<li>Point</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="row">
		<div class="col-md-6">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/band/point/')?>create_club" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E17" class="control-label">Create Club</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E17" name="leaderboard_point_remark_E17" type="number" value="<?=$fs_leaderboard_point_remark_E17?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>minimum_members" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E18" class="control-label">Minimum no. of members club get SPT</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E18" name="leaderboard_point_remark_E18" type="number" value="<?=$fs_leaderboard_point_remark_E18?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>create_post_word" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E19" class="control-label">Create Post(Word)</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E19" name="leaderboard_point_remark_E19" type="number" value="<?=$fs_leaderboard_point_remark_E19?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>create_post_additional_photo" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E20" class="control-label">Create Post Additional(Photo)</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E20" name="leaderboard_point_remark_E20" type="number" value="<?=$fs_leaderboard_point_remark_E20?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>create_post_additional_video" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E21" class="control-label">Create Post Additional(Video)</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E21" name="leaderboard_point_remark_E21" type="number" value="<?=$fs_leaderboard_point_remark_E21?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>create_post_additional_attendance_sheet" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E22" class="control-label">Create Post Additional(Attendance Sheet)</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E22" name="leaderboard_point_remark_E22" type="number" value="<?=$fs_leaderboard_point_remark_E22?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>create_post_additional_location" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E23" class="control-label">Create Post Additional(Location)</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E23" name="leaderboard_point_remark_E23" type="number" value="<?=$fs_leaderboard_point_remark_E23?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>like_post" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E24" class="control-label">Like Post</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E24" name="leaderboard_point_remark_E24" type="number" value="<?=$fs_leaderboard_point_remark_E24?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>comment_post" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E25" class="control-label">Comment Post</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E25" name="leaderboard_point_remark_E25" type="number" value="<?=$fs_leaderboard_point_remark_E25?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>minimum_video_length" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E26" class="control-label">Minimum Video Length To Get SPT</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E26" name="leaderboard_point_remark_E26" type="number" value="<?=$fs_leaderboard_point_remark_E26?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>play_watch_video" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E27" class="control-label">Play/Watch Video</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E27" name="leaderboard_point_remark_E27" type="number" value="<?=$fs_leaderboard_point_remark_E27?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>invite_member" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E28" class="control-label">Invite Member Join Club</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E28" name="leaderboard_point_remark_E28" type="number" value="<?=$fs_leaderboard_point_remark_E28?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>member_join_club" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E29" class="control-label">Member Join Club</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E29" name="leaderboard_point_remark_E29" type="number" value="<?=$fs_leaderboard_point_remark_E29?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>max_club_created_each_day" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E30" class="control-label">Max Club Created Each Day</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E30" name="leaderboard_point_remark_E30" type="number" value="<?=$fs_leaderboard_point_remark_E30?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>club_owner_got_commission_if_member_create_post" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E32" class="control-label">Club - Owner group commission in % if member create post (text, photo, video)</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E32" name="leaderboard_point_remark_E32" type="number" value="<?=$fs_leaderboard_point_remark_E32?>" class="form-control">
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
						<form action="<?=base_url('api_admin/band/point/')?>limit_create_club" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E33" class="control-label">create club get spt limit(monthly)</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E33" name="leaderboard_point_remark_E33" type="number" value="<?=$fs_leaderboard_point_remark_E33?>" class="form-control">
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
