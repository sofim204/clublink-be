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
        <h2>Akun anda telah aktif!</h2>
        <p>Akun anda telah aktif. Silahkan tutup halaman ini.</p>
        <p>Jika anda lupa kata sandi, anda bisa reset password.</p><p>&nbsp;</p>
        <p>Semoga Sukses<br />Tim <?=$this->site_name?></p>
				<pre class="" style="display:none;"><?php
				//echo $email_debug
				?></pre>
			</div>
		</div>
	</div>
	<hr>
	<div class="container">
		<div class="row">
			<div class="columns large-12">
        <h2>Your account is active now !</h2>
        <p>Your account is activated. You can close this.</p>
        <p>If you forgot the password, you can define it again.</p><p>&nbsp;</p>
        <p>Best wishes<br />The <?=$this->site_name?> team</p>
				<pre class="" style="display:none;"><?php
				//echo $email_debug
				?></pre>
			</div>
		</div>
	</div>
</section>
<!-- End Main Content -->
