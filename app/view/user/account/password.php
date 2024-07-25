<style>
.ui-datepicker {
	background-color: #fff;
	border: 1px solid #66AFE9;
	border-radius: 4px;
	box-shadow: 0 0 8px rgba(102,175,233,.6);
	display: none;
	margin-top: 4px;
	padding: 10px;
	width: 240px;
}
.ui-datepicker a,
.ui-datepicker a:hover {
	text-decoration: none;
}
.ui-datepicker a:hover,
.ui-datepicker td:hover a {
	color: #2A6496;
	-webkit-transition: color 0.1s ease-in-out;
	-moz-transition: color 0.1s ease-in-out;
	-o-transition: color 0.1s ease-in-out;
	transition: color 0.1s ease-in-out;
}
.ui-datepicker .ui-datepicker-header {
	margin-bottom: 4px;
	text-align: center;
}
.ui-datepicker .ui-datepicker-title {
	font-weight: 700;
}
.ui-datepicker .ui-datepicker-prev,
.ui-datepicker .ui-datepicker-next {
	cursor: default;
	font-family: 'Glyphicons Halflings';
	-webkit-font-smoothing: antialiased;
	font-style: normal;
	font-weight: normal;
	height: 20px;
	line-height: 1;
	margin-top: 2px;
	width: 30px;
}
.ui-datepicker .ui-datepicker-prev {
	float: left;
	text-align: left;
}
.ui-datepicker .ui-datepicker-next {
	float: right;
	text-align: right;
}
.ui-datepicker .ui-datepicker-prev:before {
	font: normal normal normal 14px/1 FontAwesome;
	content: "\f137";
}
.ui-datepicker .ui-datepicker-next:before {
	font: normal normal normal 14px/1 FontAwesome;
	content: "\f138";
}
.ui-datepicker .ui-icon {
	display: none;
}
.ui-datepicker .ui-datepicker-calendar {
	table-layout: fixed;
	width: 100%;
}
.ui-datepicker .ui-datepicker-calendar th,
.ui-datepicker .ui-datepicker-calendar td {
	text-align: center;
	padding: 4px 0;
}
.ui-datepicker .ui-datepicker-calendar td {
	border-radius: 4px;
	-webkit-transition: background-color 0.1s ease-in-out, color 0.1s ease-in-out;
	-moz-transition: background-color 0.1s ease-in-out, color 0.1s ease-in-out;
	-o-transition: background-color 0.1s ease-in-out, color 0.1s ease-in-out;
	transition: background-color 0.1s ease-in-out, color 0.1s ease-in-out;
}
.ui-datepicker .ui-datepicker-calendar td:hover {
	background-color: #eee;
	cursor: pointer;
}
.ui-datepicker .ui-datepicker-calendar td a {
	text-decoration: none;
}
.ui-datepicker .ui-datepicker-current-day {
	background-color: #4289cc;
}
.ui-datepicker .ui-datepicker-current-day a {
	color: #fff
}
.ui-datepicker .ui-datepicker-calendar .ui-datepicker-unselectable:hover {
	background-color: #fff;
	cursor: default;
}

.margintop1 {
	margin-top: 1em;
}
.table > thead > tr > th, .table > tbody > tr > th {
	background-color: #00acc1;
}
input[type="text"], input[type="password"] {
	margin-bottom: 0;
}
@media screen and (max-width: 425px) {
	.margintop1first {
		margin-top: 1em;
	}
}
</style>
<!-- Main Content -->
<section class="content account" style="padding-top: 1em;">

	<div class="container">
		<div class="row">
			<div class="columns large-12">

				<div class="row margintop1">

					<div class="columns large-4 medium-2 small-12 hidden-xs">&nbsp;</div>

					<div class="columns large-4 medium-8 small-12">
						<div class="wijet-profile-main">

							<form id="fpassword" action="<?php echo base_url('account/password/reset/'.$user->api_web_token); ?>" method="post">
								<h3 class="text-center">Reset Password</h3>
								<?php if(isset($notif)){?>
								<div class="alert-box alert"><?=$notif?></div>
								<?php } ?>
								<div class="form-group">
									<label for="ipassword">Password
										<input id="ipassword" name="password" minlength="8" type="password" required  />
									</label>
									<small id="spassword" class="error" style="display:none;">Invalid entry</small>
								</div>
								<div class="form-group">
									<label for="irepassword">Confirm Password
										<input id="irepassword" name="repassword" minlength="8" type="password" required  />
									</label>
									<small id="srepassword" class="error" style="display:none;">Invalid entry</small>
								</div>
								<br />
								<input type="submit" class="button expand" value="Change Password" />
							</form>

						</div>
					</div>

					<div class="columns large-4 medium-2 small-12">&nbsp;</div>

				</div>

				<br /><br />
			</div>
		</div>
	</div>
</section>
<!-- End Main Content -->
