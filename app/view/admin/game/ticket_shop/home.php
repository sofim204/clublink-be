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
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="" href="<?=base_url_admin('game/ticket_shop/tambah/')?>" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Game</li>
		<li>Ticket Shop</li>
		<input type="hidden" id="user_role" value="<?=$user_role; ?>" />
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="row">
		<div class="col-md-6">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/game/ticket_shop/')?>every_like_ticket" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E11" class="control-label">Clicking like/dislike 5 times converts to 1 ticket</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E11" name="leaderboard_point_remark_E11" type="number" value="<?=$fs_leaderboard_point_remark_E11?>" class="form-control">
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
						<form action="<?=base_url('api_admin/game/ticket_shop/')?>total_get_ticket" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_leaderboard_point_remark_E12" class="control-label">1 ticket is obtained from 5 clicks of like/dislike</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_leaderboard_point_remark_E12" name="leaderboard_point_remark_E12" type="number" value="<?=$fs_leaderboard_point_remark_E12?>" class="form-control">
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
						<form action="<?=base_url('api_admin/game/ticket_shop/')?>max_ticket_get_like_dislike" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_game_remark_I2" class="control-label">Max ticket get from like/dislike</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_game_remark_I2" name="game_remark_I2" type="number" value="<?=$fs_game_remark_I2?>" class="form-control">
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
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-users"></i> <strong>Ticket Shop</strong></h2>
				</div>
				<div class="block-section first-section">
					<div class="table-responsive">
						<table id="drTable" class="table table-vcenter table-condensed table-bordered">
							<thead>
								<tr style="background-color: #FFFFFF;">
									<th class="text-center" width="50px">No.</th>
									<th class="text-center">ID</th>
									<th>Earned Ticket</th>
									<th>Price (SPT)</th>
									<th>Date</th>
									<th>Active</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>ROCK PAPER SCISSORS</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/game/ticket_shop/')?>total_free_ticket_rock_paper_scissors" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_game_remark_I1" class="control-label">Total free ticket</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_game_remark_I1" name="game_remark_I1" type="number" value="<?=$fs_game_remark_I1?>" class="form-control">
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
						<form action="<?=base_url('api_admin/game/ticket_shop/')?>maintenance_rock_paper_scissors" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_app_config_remark_C17" class="control-label">Maintenance</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<select id="fs_app_config_remark_C17" name="app_config_remark_C17" class="form-control">
											<option value="on" <?php if($fs_app_config_remark_C17 == 'on') echo"selected"; ?>>ON</option>
											<option value="off" <?php if($fs_app_config_remark_C17 == 'off') echo"selected"; ?>>OFF</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
						<form action="<?=base_url('api_admin/game/ticket_shop/')?>max_win_rock_paper_scissors_per_day" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_app_config_remark_I5" class="control-label">Max Win Per Day</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_game_remark_I5" name="game_remark_I5" type="number" value="<?=$fs_game_remark_I5?>" class="form-control">
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
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>SHOOTING FIRE</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/game/ticket_shop/')?>total_free_ticket_shooting_fire" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_game_remark_I3" class="control-label">Total free ticket</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_game_remark_I3" name="game_remark_I3" type="number" value="<?=$fs_game_remark_I3?>" class="form-control">
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
						<form action="<?=base_url('api_admin/game/ticket_shop/')?>maintenance_shooting_fire" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_app_config_remark_C18" class="control-label">Maintenance</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<select id="fs_app_config_remark_C18" name="app_config_remark_C18" class="form-control">
											<option value="on" <?php if($fs_app_config_remark_C18 == 'on') echo"selected"; ?>>ON</option>
											<option value="off" <?php if($fs_app_config_remark_C18 == 'off') echo"selected"; ?>>OFF</option>
										</select>
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
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<label for="" class="control-label"><h4><strong>SELLON MATCH</strong></h4></label>
					</div>
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/game/ticket_shop/')?>total_free_ticket_sellon_match" method="post" class="form-horizontal form-setup">
							<div class="form-group">
								<div class="col-md-7">
									<label for="fs_game_remark_I4" class="control-label">Total free ticket</label>
								</div>
								<div class="col-md-5">
									<div class="input-group">
										<input id="fs_game_remark_I4" name="game_remark_I4" type="number" value="<?=$fs_game_remark_I4?>" class="form-control">
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
