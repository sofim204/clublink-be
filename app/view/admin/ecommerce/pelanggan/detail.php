<style>
	.btn-back {
        width: 85px;
        cursor: pointer;
        background: #F9F5F5;
        border: 1px solid #999;
        outline: none;
		color: #222121;
        transition: .3s ease;
    }

    .btn-back:hover {
        transition: .3s ease;
        background: #DD8A0D;
        border: 1px solid transparent;
        color:#FFF;
    }

	#userImage {
		cursor: zoom-in;
		transition: transform .3s;
	}

	#userImage:hover { transform: scale(1.1); }

	.modal-preview-image {
		display: none;
		position: fixed;
		z-index: 1;
		padding-top: 20px;
		padding-bottom: 20px;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: rgb(0, 0, 0);
		background-color: rgba(0, 0, 0, 0.7);
	}

	.modal-content {		
		display: block;
		margin: 8% auto;
		padding: 0px;
		width: 50%;
		max-width: 700px;
		border-radius: 10px;
	}

	#caption {
		margin-bottom: 10px;
		width: 100%;
		max-width: 700px;
		text-align: center;
		color: #000000;
		font-size: 15px;
	}

	@-webkit-keyframes zoom {
		from {-webkit-transform: scale(0)}
		to {-webkit-transform: scale(1)}
	}

	@keyframes zoom {
		from {transform: scale(0)}
		to {transform: scale(1)}
	}

	.modal-content, #caption {
		-webkit-animation-name: zoom;
		-webkit-animation-duration: 0.6s;
		animation-name: zoom;
		animation-duration: 0.6s;
	}

	.close {
		color: #E6E3E2;
		float: right;
		font-size: 50px;
		font-weight: bold;
		transition: 0.3s;
		opacity: 0.8;
		border: 2px solid #000000;
		background-color: #000000;
		width: 50px;
		text-align: center;
		border-radius: 0px 8px 0px 10px;
	}

	.close:hover,
	.close:focus {
		color: #E4721C;
		text-decoration: none;
		cursor: pointer;
		opacity: 0.9;
	}

	@media only screen and (max-width: 700px) {
		.modal-content {
			width: 100%;
		}
	}
</style>
<!-- Page content -->
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-4">
				<div class="btn-group ">
					<!-- <a id="aback" href="<?=base_url_admin('ecommerce/pelanggan/')?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a> -->
				</div>
			</div>
			<div class="col-md-8">
				<?php if($user_role == "marketing" || $user_role == "customer_service") { ?>
					&nbsp;
				<?php } else { ?>
					<div class="btn-group pull-right">
						<button id="bemail_lupa" type="button" class="btn btn-info text-left"><i class="fa fa-key"></i> Forgot Password</button>
						<button id="bactivated" type="button" class="btn btn-success text-left"><i class="fa fa-play"></i> Set Active</button>
						<button id="bdeactivated" type="button" class="btn btn-danger text-left"><i class="fa fa-stop"></i> Set inactive</button>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li><a href="<?=base_url_admin("ecommerce/pelanggan/")?>">Customer</a></li>
		<li>Detail</li>
		<li><?=$pelanggan->fnama?></li>
	</ul>
	<!-- END Static Layout Header -->

	<?php
		if(isset($pelanggan->telp)) $pelanggan->telp = $pelanggan->telp;
		else $pelanggan->telp = " -";
	?>

	<!-- User Profile Content -->
	<div class="row">
		<div class="col-md-6">
			<!-- First Row -->
			<div class="col-md-12">
				<div class="block">
					<!-- Account Status Title -->
					<div class="block-title">
						<h2><i class="fa fa-user"></i> User <strong>Profile</strong></h2>
					</div>
					<!-- END Account Status Title -->

					<!-- Account Stats Content -->
					<div class="row block-section text-center">
						<div class="col-md-2">&nbsp;</div>
						<div class="col-md-5">
							<!-- <a href="<?=base_url($pelanggan->image)?>" class="gallery-link" title="Image Info" target="_blank">
								<img src="<?=base_url($pelanggan->image)?>" class="img-responsive" alt="dp-pelanggan" onerror="this.onerror=null;this.src='<?=base_url()?>media/produk/default.png';" />
							</a> -->
							<div>
								<img id="userImage" title="<?=$this->__e($pelanggan->fnama.' '.$pelanggan->lnama)?>" src="<?=base_url($pelanggan->image)?>" class="img-responsive" style="width: 50%;" alt="dp-pelanggan" onerror="this.onerror=null;this.src='<?=base_url()?>media/produk/default.png';" />
							</div>
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
								<!-- by Muhammad Sofi 29 December 2021 15:00 | Separate between nation code no and telp no -->
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
									<?php if (!empty($pelanggan->is_confirmed)) { ?>
										<strong class="text-success">Yes</strong>
									<?php } else { ?>
										<strong class="text-danger">not yet</strong>
										&nbsp;&nbsp;&nbsp;<a id="bemail_konfirmasi" href="#" class="text-info"><i class="fa fa-envelope"></i> Send a Verification Link</a>
									<?php } ?>
								</td>
							</tr>
							<tr id="<?=$pelanggan->api_mobile_token?>">
								<td class="text-right">Status</td>
								<td>
									<?php if (!empty($pelanggan->is_active)) { ?>
										<strong class="text-success">Active</strong>
									<?php } else { ?>
										<strong class="text-danger">Inactive</strong>
									<?php } ?>
									<?php if ($pelanggan->is_active == 0) { ?>
										<?php if ($pelanggan->is_permanent_inactive == 0) { ?>
											<strong class="text-danger">(Permanent)</strong>
										<?php } elseif ($pelanggan->is_permanent_inactive == 1) { ?>
											<strong class="text-danger">(Temporary)</strong>
										<?php } ?>
									<?php } ?>									
								</td>
							</tr>
							<?php if ($pelanggan->is_active == 0) { ?>
								<?php if ($pelanggan->is_permanent_inactive == 0) { ?>
									<tr>
										<td class="text-right">Permanent Inactive By</td>
										<td><?=$pelanggan->permanent_inactive_by?></td>
									</tr>
								<?php } ?>							
							<?php } ?>
							<?php if ($pelanggan->is_active == 0) { ?>
								<?php if ($pelanggan->is_permanent_inactive == 0) { ?>
									<tr>
										<td class="text-right">Permanent Inactive Date</td>
										<td><?=date('d F Y H:i:s', strtotime($pelanggan->permanent_inactive_date))?></td>
									</tr>
								<?php } ?>							
							<?php } ?>
							<?php if ($pelanggan->is_active == 0) { ?>
								<tr>
									<td class="text-right">Reason Inactive</td>
									<td><?=$pelanggan->inactive_text?></td>
								</tr>							
							<?php } ?>
							<!-- by Muhammad Sofi 15 February 2022 11:07 | show verification number in detail customer -->
							<tr>
								<td class="text-right">Verification Number</td>
								<?php if (!empty($verification_number->verif_number)) { ?>
									<td><?=$verification_number->verif_number?></td>
								<?php } else { ?>
									<td>-</td>
								<?php } ?>
							</tr>
							<tr>
								<td class="text-right">Country Origin</td>
								<td><?=$pelanggan->country_origin?></td>
							</tr>
						</tbody>
					</table>
					<!-- END Account Status Content -->

				</div>
				<!-- END Info Block -->

			</div>
			<!-- END First Row -->
			<!-- Second Row -->
			<div class="col-md-12">
				<div class="block">
						<!-- Account Status Title -->
						<div class="block-title">
							<h2><i class="fa fa-road"></i> Address <strong>Detail</strong></h2>
						</div>
						<!-- END Account Status Title -->

						<!-- Account Stats Content -->
						<?php foreach($detail_address as $new) { 
							if($new->address_status=="A0"){$status="Basic Address";}
							if($new->address_status=="A1"){$status="Billing Address";}
							if($new->address_status=="A2"){$status="Receiving Address";}
							if($new->address_status=="A3"){$status="Pickup Address";}?>
						<table class="table table-borderless table-striped table-vcenter">
								<tbody>
									<tr>
										<td class="text-right" style="width: 30%;"><strong>Address Name: </strong></td>
										<td>
											<strong><strong><?=$new->judul?></strong></strong>
										</td>
									</tr>
									<tr>
										<td class="text-right">Addres Detail: </td>
										<td class="">
											<?=$new->catatan?>
										</td>
									</tr>
									<tr>
										<td class="text-right">Address: </td>
										<td>
											<?=$new->alamat2_full?>
										</td>
									</tr>
									<tr>
										<td class="text-right">Zipcode: </td>
										<td>
											<?=$new->kodepos?>
										</td>
									</tr> 
									<tr>
										<td class="text-right">Type: </td>
										<td>
											<?=$status?>
										</td>
									</tr>
									<tr>
										<td class="text-right">Latitude: </td>
										<td>
											<?=$new->latitude?>
										</td>
									</tr>
									<tr>
										<td class="text-right">Longitude: </td>
										<td>
											<?=$new->longitude?>
										</td>
									</tr>
								</tbody>
							</table>
						<?php }?>
				</div>
				<!-- END Info Block -->
			</div>
			<!-- END First Row -->
		</div>
		
		<div class="col-md-6">
			<!-- First Row -->
			<div class="col-md-12">
				<div class="block">
					<!-- Account Status Title -->
					<div class="block-title">
						<h2><i class="fa fa-bank"></i> Bank <strong>Account</strong></h2>
					</div>
					<!-- END Account Status Title -->

					<!-- Account Stats Content -->
					<form id="form_bank_account" method="post">
						<input type="hidden" name="nation_code" value="<?=$pelanggan->nation_code?>" class="form-control form-bank-acc" disabled required />
						<input type="hidden" name="b_user_id" value="<?=$pelanggan->id?>" class="form-control form-bank-acc" disabled required />

						<table class="table table-borderless table-striped table-vcenter">
							<tbody>
								<tr>
									<td class="text-right" style="width: 30%;">Bank Name</td>
									<td>
										<select name="a_bank_id" class="form-control form-bank-acc" disabled required>
											<option value="">--</option>
											<?php foreach ($bank_list as $bl) { ?>
											<option value="<?=$bl->id?>" 
												<?=(isset($bank_account->a_bank_id) ? ($bl->id == $bank_account->a_bank_id ? 'selected' : '') : '') ?>><?=$bl->nama?></option>
											<?php } ?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="text-right">Account Number</td>
									<td class="">
										<input type="text" name="nomor" value="<?=$this->__e($bank_account->nomor)?>" class="form-control form-bank-acc" disabled required />
									</td>
								</tr>
								<tr>
									<td class="text-right">Account Holder</td>
									<td>
										<input type="text" name="nama" value="<?=$this->__e($bank_account->nama)?>" class="form-control form-bank-acc" disabled required />
									</td>
								</tr>
								<tr>
									<td class="text-right"> &nbsp;</td>
									<td>
										<div class="btn-group">
											<button type="button" class="btn btn-default btn-bank-acc-locker"><i class="fa fa-lock"></i> Locked</button>
											<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Changes</button>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<!-- END Account Status Content -->
					</form>
				</div>
				<!-- END Info Block -->
			</div>
			<!-- END First Row -->
			<!-- Second Row -->
			
			<!-- END First Row -->
		</div>

		<div class="col-md-5" id="container_total_post">
			<table id="clubTable" border="2" style="margin-left: 18px; background-color: #FFFFFF;">
				<thead>
				<tr id="header_table" class="hidden" style="font-size: 18px;">
					<th style="padding: 10px; width: 190px; text-align: center;">Club Name</th>
					<th style="padding: 10px; text-align: center;">Total Posts</th>
				</tr>
				</thead>
				<tbody id="clubData" style="padding: 5px;">
				</tbody>
			</table>
		</div>
	</div>
	<!-- END User Profile Content -->
</div>
<!-- END Page Content -->

<div id="modalPreview" class="modal-preview-image">
  <div class="modal-content">
	<img id="image-preview" style="width: 30%; margin:10px 0px 10px 35%;">
    <span class="close">&times;</span>
	<div id="caption"><?=base_url($pelanggan->image)?></div>
  </div>
</div>