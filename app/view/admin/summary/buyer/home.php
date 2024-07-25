<!-- Page content -->
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-4">
				<div class="btn-group ">
					<a id="aback" href="<?=base_url_admin('ecommerce/pelanggan/')?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
			<div class="col-md-8">
				<div class="btn-group pull-right">
					<button id="bemail_lupa" type="button" class="btn btn-info text-left"><i class="fa fa-key"></i> Forgot Password</button>
					<button id="bactivated" type="button" class="btn btn-success text-left"><i class="fa fa-play"></i> Set Active</button>
					<button id="bdeactivated" type="button" class="btn btn-danger text-left"><i class="fa fa-stop"></i> Set inactive</button>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Summary</li>
		<li>Buyer</li>
		<li><?=$pelanggan->fnama?></li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- User Profile Content -->
	<div class="row">
		<!-- First Column -->
		<div class="col-md-5">

			<div class="block">
					<!-- Account Status Title -->
					<div class="block-title">
						<h2><i class="fa fa-user"></i> Buyer <strong>Profile</strong></h2>
					</div>
					<!-- END Account Status Title -->

					<!-- Account Stats Content -->
					<div class="row block-section text-center">
						<div class="col-md-2">&nbsp;</div>
						<div class="col-md-5">
							<a href="<?=base_url($pelanggan->image)?>" class="gallery-link" title="Image Info" target="_blank">
								<img src="<?=base_url($pelanggan->image)?>" class="img-responsive" alt="dp-pelanggan" />
							</a>
						</div>
						<div class="col-md-2">&nbsp;</div>
					</div>
					<table class="table table-borderless table-striped table-vcenter">
						<tbody>
							<tr>
								<td class="text-right" style="width: 30%;">Name</td>
								<td><strong><?=$this->__e($pelanggan->fnama.' '.$pelanggan->lnama)?></strong></td>
							</tr>
							<tr>
								<td class="text-right">Email</td>
								<td class=""><strong><?=$this->__e($pelanggan->email)?></strong></td>
							</tr>
							<tr>
								<td class="text-right">Phone Number</td>
								<!-- by Muhammad Sofi 9 February 2022 13:55 | bug fix code replace 65 to empty string-->
								<td><strong><?='(+'.$pelanggan->nation_code.')'.$pelanggan->telp?></strong></td> 
							</tr>
							<tr>
								<td class="text-right">Register Date</td>
								<td><strong><?=$pelanggan->cdate?></strong></td>
							</tr>
							<tr>
								<td class="text-right">Email Confirmed?</td>
								<td>
									<?php if(!empty($pelanggan->is_confirmed)){ ?>
									<strong class="text-success">Yes</strong>
									<?php }else{ ?>
									<strong class="text-danger">not yet</strong>
									&nbsp;&nbsp;&nbsp;<a id="bemail_konfirmasi" href="#" class="text-info"><i class="fa fa-envelope"></i> Sent Activation Link</a>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td class="text-right">Status</td>
								<td>
									<?php if(!empty($pelanggan->is_active)){ ?>
									<strong class="text-success">Active</strong>
									<?php }else{ ?>
									<strong class="text-danger">Inactive</strong>
									<?php } ?>
								</td>
							</tr>
						</tbody>
					</table>
					<!-- END Account Status Content -->

			</div>
			<!-- END Info Block -->

		</div>
		<!-- END First Column -->

		<!-- Second Column -->
		<div class="col-md-7">
      <div class="block">
        <!-- Account Status Title -->
        <div class="block-title">
          <h2><i class="fa fa-line-chart"></i> Summary <strong></strong></h2>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="row push">
              <div class="col-xs-6">
                <h3><strong id="buyer-order-count" class="animation-fadeInQuick">45.000</strong><br><small class="text-uppercase animation-fadeInQuickInv"><a href="javascript:void(0)">Total Orders</a></small></h3>
              </div>
              <div class="col-xs-6">
                <h3><strong id="buyer-confirmed-count" class="animation-fadeInQuick">1.520.000</strong><br><small class="text-uppercase animation-fadeInQuickInv"><a href="javascript:void(0)">Confirmed Order</a></small></h3>
              </div>
              <div class="col-xs-6">
                <h3><strong id="buyer-rejected-count" class="animation-fadeInQuick">28.000</strong><br><small class="text-uppercase animation-fadeInQuickInv"><a href="javascript:void(0)">Rejected Order</a></small></h3>
              </div>
              <div class="col-xs-6">
                <h3><strong id="buyer-product-count" class="animation-fadeInQuick">3.5%</strong><br><small class="text-uppercase animation-fadeInQuickInv"><a href="javascript:void(0)">Product Count</a></small></h3>
              </div>
              <div class="col-xs-6">
                <h3><strong id="buyer-freeproduct-count" class="animation-fadeInQuick">4.250</strong><br><small class="text-uppercase animation-fadeInQuickInv"><a href="javascript:void(0)">Free Product Count</a></small></h3>
              </div>
            </div>
          </div>
        </div>
      </div>
		</div>
		<!-- END Second Column -->
	</div>
	<!-- END User Profile Content -->
</div>
<!-- END Page Content -->
