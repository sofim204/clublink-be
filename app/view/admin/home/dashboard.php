<?php
	$admin_name = $sess->admin->username;
	$welcome_message = '';
	if(isset($sess->admin->nama)) if(strlen($sess->admin->nama)>1) $admin_name = $sess->admin->nama;
	if(isset($sess->admin->welcome_message)) if(strlen($sess->admin->welcome_message)>1) $welcome_message = $sess->admin->welcome_message;
?>
<style>
	.themed-background.biru {
		background-color: #1bbae1;
	}
	.themed-background.merah {
		background-color: #e74c3c;
	}
	table#drTable tr:hover {
		background-color: #EFBF65;
	}

	/* .ui-datepicker-calendar {
		display: none;
	} */

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

	.total-sales-all-wrapper {
		margin-left: -30px; 
		background-color: #FFFFFF; 
		text-align:left;
	}

	.total-sales-all-label {
		color: #000000; 
		font-size: 16px; 
		margin-top: 5px; 
		padding: 12px;
		border: 0.8px solid #DADADA; 
	}

</style>
<div id="page-content">

	<!-- by Donny Dennison - 25 january 2021 14:52 -->
	<!-- add need action column in dashboard -->
	<!-- START by Donny Dennison - 25 january 2021 14:52 -->

	<!-- Overview Block-->
<!-- <?php if($user_role == "customer_service") { ?>
	<div style="display: flex; flex-direction: column; justify-content: center; align-items:center; margin-top: 10rem;">
		<h1 style="font-weight: bolder;">Hello, Customer Service Sellon</h1>
		<img src="<?= base_url("media/sellon_logo.png"); ?>" class="img-responsive">
	</div>
<?php } else { ?>	 -->
	<?php if($user_role == "marketing") { ?>
		&nbsp;
	<?php } else { ?>
		<div class="block full">
			<div class="block-title">
				<h2><strong>Need Action</strong> </h2>
			</div>

			<div class="row" style="margin-left: 3px;">

				<a href="<?= base_url_admin("crm/produkreport"); ?>">
					<div class="col-md-3" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; margin-right: 0px; padding-top: 5px;">
						<div class="col-md-2">
							<img src="<?= base_url("media/icon/home_reported_product.png"); ?>" class="center" style="width: 32px; padding-top: 6px;">
						</div>
						<div class="col-md-10" style="padding-right: 0px;">
							<label style="color: #000000; font-size: 16px;"> <span id="reported_product_total"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
							<br>
							<label style="color: #8A8A8A; font-size: 12px;"> Reported Product </label>
						</div>
					</div>
				</a>

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

				<!-- start temporary disabled for indonesia -->
				<!-- <a href="<?= base_url_admin("ecommerce/rejectseller"); ?>">
					<div class="col-md-3" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; margin-right: 0px;">
						<div class="col-md-2">
							<img src="<?= base_url("media/icon/home_rejected_by_seller.png"); ?>" class="center" style="width: 32px; padding-top: 6px;">
						</div>

						<div class="col-md-10" style="padding-right: 0px;">
							<label style="color: #000000; font-size: 16px;"> <span id="rejected_by_seller_total"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
							<br>
							<label style="color: #8A8A8A; font-size: 12px;"> Rejected by Seller </label>
						</div>
					</div>
				</a> -->

				<!-- <a href="<?= base_url_admin("ecommerce/rejectbuyer"); ?>">
					<div class="col-md-3" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px;">
						<div class="col-md-2">
							<img src="<?= base_url("media/icon/home_rejected_item_by_buyer.png"); ?>" class="center" style="width: 32px; padding-top: 6px;">
						</div>

						<div class="col-md-10" style="padding-right: 0px;">
							<label style="color: #000000; font-size: 16px;"> <span id="rejected_item_by_buyer_total"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
							<br>
							<label style="color: #8A8A8A; font-size: 12px;"> Rejected Item(s) by Buyer </label>
						</div>
					</div>
				</a> -->
				<!-- end temporary disabled for indonesia -->

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
			<br>
			<div class="row">
				
			</div>

		</div>
	<?php } ?>
	<!-- END Overview Block -->

	<!-- END by Donny Dennison - 25 january 2021 14:52 -->
	<!-- 
	<div class="row text-center">
		<!- - earning today widget - ->
		<div class="row" style="margin-bottom:16px;">
			<!- - EDIT By Aditya Adi Prabowo 5/8/2020 16:25
				 Improve Filter Date In Dasbord And Change Parameter General Report
				 START IMPROVE - ->
			<form action="<?=base_url("api_admin/home")?>" method="POST" enctype="multipart/form-data" id="date_filter" class="date_filter">
				<div class="col-md-1"> </div>
				<div class="col-md-3">
					<label for="ifcdate_start">From Order Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="ifcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" />
					</div>
				</div>
				<div class="col-md-3">
					<label for="ifcdate_max">To Order Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="ifcdate_max" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" />
					</div>
				</div>
				<div class="col-md-2">
					<label for="areset_do">&nbsp;</label>
					<button id="areset_do" class="btn btn-warning btn-block"> Reset</button>
				</div>
				<div class="col-md-2">
					<label for="afilter_do">&nbsp;</label>
					<button id="afilter_do" href="#" class="btn btn-info btn-block"> Filter</button>
				</div>
			</div>
		</form>
		<!- - END IMPROVE - ->
		<div class="col-sm-6 col-lg-4">
			<a href="<?=base_url_admin("ecommerce/payment")?>" class="widget widget-hover-effect2">
				<div class="widget-extra themed-background">
					<h4 class="widget-content-light"><strong>Unpaid to Seller </strong></h4>
				</div>
				<div class="widget-extra-full"><span id="earning-this-month" class="h2 animation-expandOpen">$0</span></div>
			</a>
		</div>
		<!- - end earning today widget - ->
		<!- - earning today widget - ->
		<div class="col-sm-6 col-lg-4">
			<a href="javascript:void(0)" class="widget widget-hover-effect2">
				<div class="widget-extra themed-background biru">
					<h4 class="widget-content-light"><strong>Sales </strong></h4>
				</div>
				<div class="widget-extra-full"><span id="sales-this-month"  class="h2 animation-expandOpen">$0</span></div>
			</a>
		</div>
		<!- - end earning today widget - ->
		<!- - Pending widget - ->
		<div class="col-sm-6 col-lg-4">
			<a href="javascript:void(0)" class="widget widget-hover-effect2">
				<div class="widget-extra themed-background merah">
					<h4 class="widget-content-light"><strong>Profit </strong></h4>
				</div>
				<div class="widget-extra-full"><span id="unpaid-this-month"  class="h2 animation-expandOpen"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span></div>
			</a>
		</div>
		<!- - end earning today widget - ->
	</div> <!- - end row- ->  -->

	<!-- by Donny Dennison - 26 january 2021 16:04 -->
	<!-- add need action column in dashboard -->
	<!-- START by Donny Dennison - 26 january 2021 16:04 -->

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

			<a href="<?= base_url_admin("community/product"); ?>">
				<div class="col-md-3" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; margin-right: 0px; padding-top: 5px;">
					<div class="col-md-2">
						<img src="<?= base_url("media/icon/home_reported_discussion.png"); ?>" class="center" style="width: 32px; padding-top: 6px;">
					</div>
					<div class="col-md-10" style="padding-right: 0px;">
						<label style="color: #000000; font-size: 16px;"> <span id="total_active_product"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
						<br>
						<label style="color: #8A8A8A; font-size: 12px;"> Product </label>
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

	<div class="block full" style="display: none;">
		<div class="block-title">
			<h2><strong>Order Summary</strong> </h2>
		</div>

		<div class="row">

			<!-- earning today widget -->
			<div class="row" style="margin-bottom:16px;">
				<!-- EDIT By Aditya Adi Prabowo 5/8/2020 16:25
					 Improve Filter Date In Dasbord And Change Parameter General Report
					 START IMPROVE -->
				<form action="<?=base_url("api_admin/home")?>" method="POST" enctype="multipart/form-data" id="date_filter" class="date_filter">
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input id="ifcdate_start" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" />
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input id="ifcdate_max" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" />
						</div>
					</div>
					<div class="col-md-2">
						<button id="afilter_do" href="#" class="btn btn-info btn-block"> Filter</button>
					</div>
					<div class="col-md-2">
						<button id="areset_do" class="btn btn-warning btn-block"> Reset</button>
					</div>
				</div>
			</form>
			<!-- END IMPROVE -->

			<a href="<?= base_url_admin("ecommerce/payment"); ?>">
				<div class="col-md-4" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px;">
					<div class="col-md-10">
						<label style="color: #000000; font-size: 16px;"> <span id="earning-this-month">$0</span> </label>
						<br>
						<label style="color: #E64646; font-size: 16px;"> Unpaid to Seller </label>
					</div>
				</div>
			</a>

			<!-- end earning today widget -->

			<!-- earning today widget -->

			<a href="javascript:void(0)" style="color: #FCFBFA;">
				<div class="col-md-4" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px;">
					<div class="col-md-10">
						<label style="color: #000000; font-size: 16px;"> <span id="sales-this-month">$0</span> </label>
						<br>
						<label style="color: #7ABCE7; font-size: 16px;"> Sales </label>
					</div>
				</div>
			</a>

			<!-- end earning today widget -->

			<!-- Pending widget -->

			<a href="javascript:void(0)">
				<div class="col-md-4" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px;">
					<div class="col-md-10">
						<label style="color: #000000; font-size: 16px;"> <span id="unpaid-this-month">$0</span> </label>
						<br>
						<label style="color: #0F8855; font-size: 16px;"> Profit </label>
					</div>
				</div>
			</a>

			<!-- end earning today widget -->

		</div> <!-- end row-->

	</div>

	<!-- START by Donny Dennison - 26 january 2021 16:04 -->

	<!-- Overview Block-->
	<div class="block full" style="display: none;">
		<div class="block-title">
			<div class="text-center">
				<h2><strong>Overview</strong> </h2>
			</div>
		</div>
		<!-- END eShop Overview Title -->

		<!-- eShop Overview Content -->
		<div class="row">
			<div class="col-md-12">
				<!-- Flot Charts (initialized in js/pages/ecomDashboard.js), for more examples you can check out http://www.flotcharts.org/ -->
				<div id="chart-overview" style="height: 350px;"></div>
			</div>
		</div>
	</div>
	<!-- END Overview Block -->

	<!-- by Donny Dennison - 26 january 2021 16:35 -->
	<!-- add need action column in dashboard -->
	<!-- START by Donny Dennison - 26 january 2021 16:35 -->
	<!-- 
	<div class="row">
		<div class="col-lg-6">
			<!- - Latest Orders Block - ->
			<div class="block">
				<!- - Latest Orders Title - ->
				<div class="block-title">
					<div class="block-options pull-right">
						<a href="<?=base_url_admin("ecommerce/transaction/buyer/")?>" class="btn btn-alt btn-sm btn-default" data-toggle="tooltip" title="Show All"><i class="fa fa-eye"></i></a>
					</div>
					<h2><strong>Latest</strong> Orders</h2>
				</div>
				<!- - END Latest Orders Title - ->

				<!- - Latest Orders Content - ->
				<div class="table-responsive">
					<table id="table-order-latest" class="table table-borderless table-striped table-vcenter table-bordered">
						<tbody>
							<tr>
								<td class="text-center" style="width: 100px;"><a href="javascript:void(0)"><strong>ORD.685116</strong></a></td>
								<td class="hidden-xs"><a href="javascript:void(0)">Gerald Berry</a></td>
								<td class="hidden-xs">Paypal</td>
								<td class="text-right"><strong>$65,00</strong></td>
								<td class="text-right"><span class="label label-success">Delivered</span></td>
							</tr>
							<tr>
								<td class="text-center"><a href="javascript:void(0)"><strong>ORD.685115</strong></a></td>
								<td class="hidden-xs"><a href="javascript:void(0)">Patrick Bates</a></td>
								<td class="hidden-xs">Bank wire</td>
								<td class="text-right"><strong>$268,00</strong></td>
								<td class="text-right"><span class="label label-danger">Canceled</span></td>
							</tr>
							<tr>
								<td class="text-center"><a href="javascript:void(0)"><strong>ORD.685114</strong></a></td>
								<td class="hidden-xs"><a href="javascript:void(0)">Ethan Greene</a></td>
								<td class="hidden-xs">Paypal</td>
								<td class="text-right"><strong>$362,00</strong></td>
								<td class="text-right"><span class="label label-success">Delivered</span></td>
							</tr>
							<tr>
								<td class="text-center"><a href="javascript:void(0)"><strong>ORD.685113</strong></a></td>
								<td class="hidden-xs"><a href="javascript:void(0)">Bruce Hicks</a></td>
								<td class="hidden-xs">Paypal</td>
								<td class="text-right"><strong>$23,00</strong></td>
								<td class="text-right"><span class="label label-warning">Processing</span></td>
							</tr>
							<tr>
								<td class="text-center"><a href="javascript:void(0)"><strong>ORD.685112</strong></a></td>
								<td class="hidden-xs"><a href="javascript:void(0)">Harry Burke</a></td>
								<td class="hidden-xs">Bank wire</td>
								<td class="text-right"><strong>$1360,00</strong></td>
								<td class="text-right"><span class="label label-success">Delivered</span></td>
							</tr>
							<tr>
								<td class="text-center"><a href="javascript:void(0)"><strong>ORD.685111</strong></a></td>
								<td class="hidden-xs"><a href="javascript:void(0)">Nancy Rose</a></td>
								<td class="hidden-xs">Bank wire</td>
								<td class="text-right"><strong>$2685,00</strong></td>
								<td class="text-right"><span class="label label-warning">Processing</span></td>
							</tr>
							<tr>
								<td class="text-center"><a href="javascript:void(0)"><strong>ORD.685110</strong></a></td>
								<td class="hidden-xs"><a href="javascript:void(0)">Helen Jensen</a></td>
								<td class="hidden-xs">Paypal</td>
								<td class="text-right"><strong>$128,00</strong></td>
								<td class="text-right"><span class="label label-success">Delivered</span></td>
							</tr>
							<tr>
								<td class="text-center"><a href="javascript:void(0)"><strong>ORD.685109</strong></a></td>
								<td class="hidden-xs"><a href="javascript:void(0)">Harry Medina</a></td>
								<td class="hidden-xs">Check</td>
								<td class="text-right"><strong>$3150,00</strong></td>
								<td class="text-right"><span class="label label-warning">Processing</span></td>
							</tr>
							<tr>
								<td class="text-center"><a href="javascript:void(0)"><strong>ORD.685108</strong></a></td>
								<td class="hidden-xs"><a href="javascript:void(0)">Ryan Hopkins</a></td>
								<td class="hidden-xs">Check</td>
								<td class="text-right"><strong>$750,00</strong></td>
								<td class="text-right"><span class="label label-success">Delivered</span></td>
							</tr>
							<tr>
								<td class="text-center"><a href="javascript:void(0)"><strong>ORD.685107</strong></a></td>
								<td class="hidden-xs"><a href="javascript:void(0)">Anthony Franklin</a></td>
								<td class="hidden-xs">Paypal</td>
								<td class="text-right"><strong>$630,00</strong></td>
								<td class="text-right"><span class="label label-danger">Canceled</span></td>
							</tr>
						</tbody>
					</table>
				</div>

				<! -- END Latest Orders Content - ->
			</div>
			<!- - END Latest Orders Block - ->
		</div>
		<div class="col-lg-6">
			<!- - Top Products Block - ->
			<div class="block">
				<!- - Top Products Title - ->
				<div class="block-title">
					<div class="block-options pull-right">
						<a href="<?=base_url_admin("ecommerce/produk/")?>" class="btn btn-alt btn-sm btn-default" data-toggle="tooltip" title="Show All"><i class="fa fa-eye"></i></a>
					</div>
					<h2><strong>Top</strong> Products</h2>
				</div>
				<!- - END Top Products Title - ->

				<!- - Top Products Content - ->
				<div class="table-responsive">
					<table id="table-best-seller" class="table table-borderless table-striped table-vcenter table-bordered">
					<tbody>
						<tr>
							<td class="text-center" style="width: 100px;"><a href="page_ecom_product_edit.html"><strong>PID.8765</strong></a></td>
							<td><a href="page_ecom_product_edit.html">iPhone 6 Plus 32GB</a></td>
							<td class="text-center"><strong>435</strong> orders</td>
							<td class="hidden-xs text-center">
								<div class="text-warning">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star-half-o"></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="text-center" style="width: 100px;"><a href="page_ecom_product_edit.html"><strong>PID.8764</strong></a></td>
							<td><a href="page_ecom_product_edit.html">Wii U</a></td>
							<td class="text-center"><strong>502</strong> orders</td>
							<td class="hidden-xs text-center">
								<div class="text-warning">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star-half-o"></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="text-center" style="width: 100px;"><a href="page_ecom_product_edit.html"><strong>PID.8763</strong></a></td>
							<td><a href="page_ecom_product_edit.html">Samsung Galaxy Note 4 32GB</a></td>
							<td class="text-center"><strong>440</strong> orders</td>
							<td class="hidden-xs text-center">
								<div class="text-warning">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star-half-o"></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="text-center" style="width: 100px;"><a href="page_ecom_product_edit.html"><strong>PID.8762</strong></a></td>
							<td><a href="page_ecom_product_edit.html">Playstation 4</a></td>
							<td class="text-center"><strong>750</strong> orders</td>
							<td class="hidden-xs text-center">
								<div class="text-warning">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star-half-o"></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="text-center" style="width: 100px;"><a href="page_ecom_product_edit.html"><strong>PID.8761</strong></a></td>
							<td><a href="page_ecom_product_edit.html">HTC One 32GB</a></td>
							<td class="text-center"><strong>420</strong> orders</td>
							<td class="hidden-xs text-center">
								<div class="text-warning">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star-half-o"></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="text-center" style="width: 100px;"><a href="page_ecom_product_edit.html"><strong>PID.8760</strong></a></td>
							<td><a href="page_ecom_product_edit.html">Xbox One</a></td>
							<td class="text-center"><strong>650</strong> orders</td>
							<td class="hidden-xs text-center">
								<div class="text-warning">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star-half-o"></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="text-center" style="width: 100px;"><a href="page_ecom_product_edit.html"><strong>PID.8762</strong></a></td>
							<td><a href="page_ecom_product_edit.html">iPad Mini Retina 64GB</a></td>
							<td class="text-center"><strong>521</strong> orders</td>
							<td class="hidden-xs text-center">
								<div class="text-warning">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star-half-o"></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="text-center" style="width: 100px;"><a href="page_ecom_product_edit.html"><strong>PID.8761</strong></a></td>
							<td><a href="page_ecom_product_edit.html">LG Tab 10.1</a></td>
							<td class="text-center"><strong>427</strong> orders</td>
							<td class="hidden-xs text-center">
								<div class="text-warning">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star-half-o"></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="text-center" style="width: 100px;"><a href="page_ecom_product_edit.html"><strong>PID.8760</strong></a></td>
							<td><a href="page_ecom_product_edit.html">Macbook Pro 15' Retina</a></td>
							<td class="text-center"><strong>392</strong> orders</td>
							<td class="hidden-xs text-center">
								<div class="text-warning">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star-half-o"></i>
								</div>
							</td>
						</tr>
						<tr>
							<td class="text-center" style="width: 100px;"><a href="page_ecom_product_edit.html"><strong>PID.8760</strong></a></td>
							<td><a href="page_ecom_product_edit.html">PS Vita</a></td>
							<td class="text-center"><strong>380</strong> orders</td>
							<td class="hidden-xs text-center">
								<div class="text-warning">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star-half-o"></i>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
				<!- - END Top Products Content - ->
			</div>
			<!- - END Top Products Block - ->
		</div>
	</div>
	<!- - END Orders and Products - ->
	-->

	<!-- END by Donny Dennison - 26 january 2021 16:35 -->
	<input type="hidden" id="reset_year_month" value="<?= $today_year_month; ?>">
	<div class="block">
		<div class="block-title">
			<h2><strong>Total Sales (Offered)</strong></h2>
		</div>
		<div class="block-section">
			<div class="row" style="margin-bottom: 4px; margin-left: 3px;">
				<!-- <div class="col-md-12">
					<a href="#">
						<div class="col-md-2" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px;">
							<div class="col-md-10">
								<label style="color: #000000; font-size: 16px; margin-top: 5px; margin-left: 35px;"> <span id="total_sales_all"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
							</div>
						</div>
					</a>
				</div> -->
				<div class="col-md-12">
					<div class="col-md-12 total-sales-all-wrapper">
						<label class="total-sales-all-label">
							<span id="total_sales_all"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span>
						</label>
					</div>
				</div>
			</div>
			<div class="row" style="display:flex; align-items:flex-end">
				<div class="col-md-4">
					<label for="ifcdate_min">From Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<!-- <input id="from_cdate_offer_summary" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="From date" readonly /> -->
						<input type="text" id="from_cdate_offer_summary" class="form-control" value="<?= $today_year_month; ?>" readonly />
					</div>
				</div>
				<div class="col-md-4">
					<label for="ifcdate_max">To Date</label>
					<div class="input-group">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<!-- <input id="to_cdate_offer_summary" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="To date" readonly /> -->
						<input type="text" id="to_cdate_offer_summary" class="form-control" value="<?= $today_year_month; ?>" readonly />
					</div>
				</div>
				<div class="col-md-2">
					<button id="filter_data_offer_summary" href="#" class="btn btn-info btn-block"> Filter</button>
				</div>
				<div class="col-md-2">
					<button id="reset_data_offer_summary" class="btn btn-warning btn-block"> Reset</button>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;">
				<div class="col-md-12">
					<div class="col-md-4" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; padding-top: 5px;">
						<div class="col-md-10">
							<label style="color: #000000; font-size: 16px;"> <span id="total_sales_seller_month"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
							<br>
							<label style="color: #E64646; font-size: 16px;"> Total Amount </label>
						</div>
					</div>
					<div class="col-md-4" style="background-color: #FCFBFA; border: 0.5px solid #DADADA; padding-left: 0px; padding-right: 0px; padding-top: 5px;">
						<div class="col-md-10">
							<label style="color: #000000; font-size: 16px;"> <span id="total_transaction_seller_month"><img src="<?= base_url("media/icon/sellon_loading.gif"); ?>" class="center" style="width: 32px; padding-top: 6px;"></span> </label>
							<br>
							<label style="color: #E64646; font-size: 16px;"> Total Transaction </label>
						</div>
					</div>
				</div>
			</div>
			<div class="table-responsive" style="margin-top: 2rem;">
				<table id="drTable" class="table table-vcenter table-condensed table-bordered" width="100%">
					<thead>
						<tr style="background-color: #FFFFFF;">
							<th>No</th>
							<th>User ID</th>
							<th>Name</th>
							<th>Total Amount as a Seller (IDR)</th>
							<th>Total Transaction as a Seller</th>
							<th>Total Amount as a Buyer (IDR)</th>
							<th>Total Transaction as a Buyer</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>

	<div id="custom_from_date_container">
		<div class="row" style="text-align: right;">
			<select name="" id="year_from_date" class="offer_summary_date" style="width: 70px;">
				<option value="">-year-</option>
				<option value="2011">2011</option>
				<option value="2012">2012</option>
				<option value="2013">2013</option>
				<option value="2014">2014</option>
				<option value="2015">2015</option>
				<option value="2016">2016</option>
				<option value="2017">2017</option>
				<option value="2018">2018</option>
				<option value="2019">2019</option>
				<option value="2020">2020</option>
				<option value="2021">2021</option>
				<option value="2022">2022</option>
				<option value="2023">2023</option>
				<option value="2024">2024</option>
				<option value="2025">2025</option>
				<option value="2026">2026</option>
				<option value="2027">2027</option>
				<option value="2028">2028</option>
				<option value="2029">2029</option>
				<option value="2030">2030</option>
				<option value="2031">2031</option>
				<option value="2032">2032</option>
				<option value="2033">2033</option>
				<option value="2034">2034</option>
				<option value="2035">2035</option>
				<option value="2036">2036</option>
				<option value="2037">2037</option>
				<option value="2038">2038</option>
				<option value="2039">2039</option>
				<option value="2040">2040</option>
			</select>
			<select name="" id="month_from_date" class="offer_summary_date" style="width: 70px;">
				<option value="">-month-</option>
				<option value="01">Jan</option>
				<option value="02">Feb</option>
				<option value="03">Mar</option>
				<option value="04">Apr</option>
				<option value="05">May</option>
				<option value="06">Jun</option>
				<option value="07">Jul</option>
				<option value="08">Aug</option>
				<option value="09">Sep</option>
				<option value="10">Oct</option>
				<option value="11">Nov</option>
				<option value="12">Des</option>
			</select>
		</div>
		<div class="row" style="margin-top: 25px;">
			<div class="" style="text-align: right;">
				<button type="button" class="btn btn-primary" id="btn_done_from_date">Done</button>
			</div>
		</div>
	</div>

	<div id="custom_to_date_container">
		<div class="row" style="text-align: right;">
			<select name="" id="year_to_date" class="offer_summary_date" style="width: 70px;">
				<option value="">-year-</option>
				<option value="2011">2011</option>
				<option value="2012">2012</option>
				<option value="2013">2013</option>
				<option value="2014">2014</option>
				<option value="2015">2015</option>
				<option value="2016">2016</option>
				<option value="2017">2017</option>
				<option value="2018">2018</option>
				<option value="2019">2019</option>
				<option value="2020">2020</option>
				<option value="2021">2021</option>
				<option value="2022">2022</option>
				<option value="2023">2023</option>
				<option value="2024">2024</option>
				<option value="2025">2025</option>
				<option value="2026">2026</option>
				<option value="2027">2027</option>
				<option value="2028">2028</option>
				<option value="2029">2029</option>
				<option value="2030">2030</option>
				<option value="2031">2031</option>
				<option value="2032">2032</option>
				<option value="2033">2033</option>
				<option value="2034">2034</option>
				<option value="2035">2035</option>
				<option value="2036">2036</option>
				<option value="2037">2037</option>
				<option value="2038">2038</option>
				<option value="2039">2039</option>
				<option value="2040">2040</option>
			</select>
			<select name="" id="month_to_date" class="offer_summary_date" style="width: 70px;">
				<option value="">-month-</option>
				<option value="01">Jan</option>
				<option value="02">Feb</option>
				<option value="03">Mar</option>
				<option value="04">Apr</option>
				<option value="05">May</option>
				<option value="06">Jun</option>
				<option value="07">Jul</option>
				<option value="08">Aug</option>
				<option value="09">Sep</option>
				<option value="10">Oct</option>
				<option value="11">Nov</option>
				<option value="12">Des</option>
			</select>
		</div>
		<div class="row" style="margin-top: 25px;">
			<div class="" style="text-align: right;">
				<button type="button" class="btn btn-primary" id="btn_done_to_date">Done</button>
			</div>
		</div>          
	</div>

	<!-- modal options -->
	<div id="modal_options" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="top: 30%;">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header modal-header-title text-center">
					<h2 class="modal-title"><strong>Options</strong></h2>
				</div>
				<div class="modal-body">
					<input type="hidden" id="user_id_toggle">
					<div class="row">
						<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
							<a id="btn_toggle_seller" href="#" class="btn btn-primary text-center" value_toggle="seller"><i class="fa fa-edit"></i> Selling History</a>
						</div>
					</div>
					<div class="row" style="margin-bottom: 6px;"></div>
					<div class="row">
						<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
							<a id="btn_toggle_buyer" href="#" class="btn btn-primary text-center" value_toggle="buyer"><i class="fa fa-edit"></i> Buying History</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- <?php } ?>	 -->

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