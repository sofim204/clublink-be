<style>
	.bordered {
		border: 1px #ededed solid;
	}
	.mp1 {
		padding: 1em;
	}
	.date-align-left{
		text-align: left;
	}
	.date-align-center{
		text-align: center;
	}
	.date-align-right{
		text-align: right;
	}
	.date-align h4{
		font-size: 14px;
	}
	.date-align p{
		font-size: 10px;
		color: #a8a8a8;
	}
	.bulksale-agent{
		margin-top: 30px;
	}
	.agent-desc h4{
		font-size: 14px;
	}
	.bulksale-image img{
		border: 1px solid #a8a8a8;
    border-radius: 5%;
		width: 100px;
		height: 100px;
    box-shadow: 1px 1px 1px 1px #a8a8a8;
		display: block;
	}
	.col-md-6 .btn-group{
		padding: 16px;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
					<a id="" href="<?=base_url_admin('ecommerce/bulksale/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">

				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Sell on me</li>
		<li><?=$produk->name?></li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<!-- Main Row -->
	<div class="row">
		<div class="col-md-12">
			<div class="block full row">
			<div class="block-title">
				<div class="row">
					<div class="col-md-6">
						<h2><strong>Sell on me Detail</strong></h2>
					</div>
					<div class="col-md-6">
						<div class="btn-group pull-right">
						  <button id="bvisit_date" type="button" class="btn btn-info">Visit Date</button>
						  <button id="bchange_status" type="button" class="btn btn-success">Change Status</button>
						  <button id="binput_price" type="button" class="btn btn-primary">Input Price</button>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<h4><strong>Product Photo</strong></h4>
				</div>
				<div class="col-md-12">
					<div class="row">
						<?php if(count($produk->fotos)>0){ foreach($produk->fotos as $foto){ ?>
							<div class="col-md-1" style="margin-right:2em">
								<div class="bulksale-image">
									<a href="<?=$this->cdn_url($foto->url_thumb)?>" target="__blank">
										<img src="<?=$this->cdn_url($foto->url_thumb) ?>" alt="" onerror="this.onerror=null;this.src='<?=$this->cdn_url('media/produk/default.png')?>';" />
									</a>
								</div>
							</div>
						<?php }} ?>
					</div>
				</div>
				<div class="col-md-12" style="margin-top:2em">
					<h4><strong>Info</strong></h4>
				</div>
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-6">
							<p><strong>Create Date</strong></p>
						</div>
						<div class="col-md-6">
							<p><?=date("j F Y H:i a",strtotime($produk->cdate)) ?></p>
						</div>
						<div class="col-md-6">
							<p><strong>Agent Status</strong></p>
						</div>
						<div class="col-md-6">
							<?php if(strlen(trim($produk->company_name)) >0){ ?>
								<p>Agent</p>
							<?php } else {?>
								<p>Guest</p>
							<?php }?>
						</div>
						<div class="col-md-6">
							<p><strong>Company Name</strong></p>
						</div>
						<div class="col-md-6">
							<?php if(strlen(trim($produk->company_name)) >0){?>
								<p><?=$produk->company_name?></p>
							<?php } else {?>
								<p>-</p>
							<?php }?>
						</div>
						<div class="col-md-6">
							<p><strong>License Number</strong></p>
						</div>
						<div class="col-md-6">
							<?php if(strlen($produk->agent_license) >0){?>
								<p><?=$produk->agent_license?></p>
							<?php } else {?>
								<p>-</p>
							<?php }?>
						</div>
						<div class="col-md-6">
							<p><strong>Description</strong></p>
						</div>
						<div class="col-md-6">
							<?php if(strlen($produk->description_long) >0){?>
								<p><?=$produk->description_long?></p>
							<?php } else {?>
								<p>&nbsp;</p>
							<?php }?>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-6">
							<p><strong>Status</strong></p>
						</div>
						<div class="col-md-6">
							<?php if(strlen($produk->action_status) >0){?>
								<p><?=$produk->action_status?></p>
							<?php } else {?>
								<p>&nbsp;</p>
							<?php }?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<p><strong>Visit Date</strong></p>
						</div>
						<div class="col-md-6">
							<p><?php
							if(!is_null($produk->vdate) && $produk->vdate != '-'){
								echo date("j F Y",strtotime($produk->vdate));
							}else{
								echo '-';
							}
							?></p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<p><strong>Price</strong></p>
						</div>
						<div class="col-md-6">
							<?php echo number_format($produk->price,2,'.',','); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<p><strong>Phone Number</strong></p>
						</div>
						<div class="col-md-6">
							<?=$seller->telp; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<p><strong>Email</strong></p>
						</div>
						<div class="col-md-6">
							<?=$seller->email; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<p><strong>Address</strong></p>
						</div>
						<div class="col-md-6">
							<!-- By Muhammad Sofi - 4 November 2021 10:00 | remark code -->
							<?=$produk->address2; ?>
						</div>
					</div>

				</div>
			</div>

		</div>

		</div>
	</div>

	<!-- END Main Row -->
</div>
