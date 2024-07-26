<?php
	$admin_name = $sess->admin->username;
	$welcome_message = '';
	if(isset($sess->admin->nama)) if(strlen($sess->admin->nama)>1) $admin_name = $sess->admin->nama;
	if(isset($sess->admin->welcome_message)) if(strlen($sess->admin->welcome_message)>1) $welcome_message = $sess->admin->welcome_message;

?>
<style>
	#custom_from_date_container, 
	#custom_to_date_container {
    	position: absolute;
		width: 185px;
		height: auto;
		background-color: #fafafa;
		border: 1px solid #d3d6d8;
		border-radius: 5px;  
		padding: 30px;
		margin: 20px;
		z-index: 199;
		display: none;
	}

</style>
<div id="page-content">
<input type="hidden" id="check_user_role" value="<?= $user_role; ?>">

<?php if($user_role != "customer_service") { ?>
	<div class="block full">
		<div class="block-title">
			<h2><strong>Need Action</strong> </h2>
		</div>

		<div class="row" style="margin-left: 3px;">
			<a href="<?= base_url_admin("crm/discuss/reported"); ?>">
				<div class="col-md-3" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; margin-right: 0px; padding-top: 5px;">
					<div class="col-md-2">
						<img src="<?= base_url("media/icon/home_reported_discussion.png"); ?>" class="center" style="width: 32px; padding-top: 6px;">
					</div>

					<div class="col-md-10" style="padding-right: 0px;">
						<label style="color: #000000; font-size: 16px;"> <span id="reported_discussion_total"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
						<br>
						<label style="color: #8A8A8A; font-size: 12px;"> Reported Discussion </label>
					</div>
				</div>
			</a>

			<a href="<?= base_url_admin("community/listing/reported/"); ?>">
				<div class="col-md-3" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; padding-top: 5px;">
					<div class="col-md-2">
						<img src="<?= base_url("media/icon/home_reported_discussion.png"); ?>" class="center" style="width: 32px; padding-top: 6px;">
					</div>

					<div class="col-md-10" style="padding-right: 0px;">
						<label style="color: #000000; font-size: 16px;"> <span id="total_reported_community_post"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
						<br>
						<label style="color: #8A8A8A; font-size: 12px;"> Reported Community Post </label>
					</div>
				</div>
			</a>
		</div>
	</div>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Total Active Data</strong> </h2>
		</div>
		<div class="row" style="margin-left: 3px;">
			<a href="<?= base_url_admin("ecommerce/user"); ?>">
				<div class="col-md-3" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; margin-right: 0px; padding-top: 5px;">
					<div class="col-md-2">
						<img src="<?= base_url("media/icon/home_reported_product.png"); ?>" class="center" style="width: 32px; padding-top: 6px;">
					</div>
					<div class="col-md-10" style="padding-right: 0px;">
						<label style="color: #000000; font-size: 16px;"> <span id="total_active_user"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
						<br>
						<label style="color: #8A8A8A; font-size: 12px;"> User </label>
					</div>
				</div>
			</a>

			<a href="<?= base_url_admin("community/listing"); ?>">
				<div class="col-md-3" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; margin-right: 0px; padding-top: 5px;">
					<div class="col-md-2">
						<img src="<?= base_url("media/icon/home_reported_discussion.png"); ?>" class="center" style="width: 32px; padding-top: 6px;">
					</div>
					<div class="col-md-10" style="padding-right: 0px;">
						<label style="color: #000000; font-size: 16px;"> <span id="total_active_community"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
						<br>
						<label style="color: #8A8A8A; font-size: 12px;"> Community </label>
					</div>
				</div>
			</a>
		</div>
	</div>

	<div class="block full">
		<div class="block-title">
			<h2><strong>Total Video</strong> </h2>
		</div>
		<div class="row" style="margin-left: 3px;">
			<a href="<?= base_url_admin("ecommerce/produk"); ?>">
				<div class="col-md-3" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; margin-right: 0px; padding-top: 5px;">
					<div class="col-md-2">
						<img src="<?= base_url("media/icon/home_reported_product.png"); ?>" class="center" style="width: 32px; padding-top: 6px;">
					</div>
					<div class="col-md-10" style="padding-right: 0px;">
						<label style="color: #000000; font-size: 16px;"> <span id="total_product_video"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
						<br>
						<label style="color: #8A8A8A; font-size: 12px;"> Product Video </label>
					</div>
				</div>
			</a>

			<a href="<?= base_url_admin("community/listing"); ?>">
				<div class="col-md-3" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; margin-right: 0px; padding-top: 5px;">
					<div class="col-md-2">
						<img src="<?= base_url("media/icon/home_reported_discussion.png"); ?>" class="center" style="width: 32px; padding-top: 6px;">
					</div>
					<div class="col-md-10" style="padding-right: 0px;">
						<label style="color: #000000; font-size: 16px;"> <span id="total_community_video"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
						<br>
						<label style="color: #8A8A8A; font-size: 12px;"> Community Video </label>
					</div>
				</div>
			</a>
		</div>
	</div>
<?php } else { ?>
	&nbsp;
<?php } ?>
	<div class="block">
		<div class="block-title">
			<h2><strong>Daily Track Record</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-3">
					<label for="from_cdate_daily_track">From Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="from_cdate_daily_track" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" readonly />
					</div>
				</div>
				<div class="col-md-3">
					<label for="to_cdate_daily_track">To Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="to_cdate_daily_track" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" readonly />
					</div>
				</div>
				<div class="col-md-1">
					<button id="filter_data_daily_track" href="#" class="btn btn-info btn-block"> Filter</button>
				</div>
				<div class="col-md-1">
					<button id="reset_data_daily_track" class="btn btn-warning btn-block"> Reset</button>
				</div>
				<div class="col-md-2">
					<button id="refresh_table_daily_track" class="btn btn-info"> Refresh Table</button>
				</div>
			</div>
			<div class="table-responsive" style="margin-top: 2rem;">
				<div class="row" style="margin-top: 15px; margin-right: 25px; float: right;">
					<label for="total_club">Total Count(Club/Post) : <span style="">(<?= $count_total_club ?> / <?= $count_total_club_post ?>)</span></label>
				</div>
				<table id="drTableDailyTrack" class="table table-vcenter table-condensed table-bordered" width="100%">
					<thead>
						<tr style="background-color: #FFFFFF;">
							<th style="width: 30px;">No</th>
							<th>Date</th>
							<th>Signup (ADR / IOS)</th>
							<!-- <th>Signup Android</th>
							<th>Signup Ios</th> -->
							<th>Community (Video)</th>
							<th>Product (Video)</th>
							<th>Club (Post)</th>
							<!-- <th>Visit (ADR / IOS)</th> -->
							<th>Visit</th>
							<!-- <th>Visit Android</th>
							<th>Visit Ios</th> -->
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>