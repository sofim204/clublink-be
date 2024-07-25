<style>
	.bordered {
		border: 1px #ededed solid;
	}
	.mp1 {
		padding: 1em;
	}
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
        color: #FFF;
    }

	a.text_link:link {
		color: #211502;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
					<!-- <a id="" href="<?=base_url_admin('ecommerce/produk/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a> -->
					<button id="b_report_product" type="button" class="btn btn-danger">Report Product</button>
					<div class="" id="message_status_report" style="display:none; background-color: #ed9111; font-size: 14px; font-weight: 500; color: white; padding: 6px 12px; border-radius: 4px;">This Post Already Reported</div>
				</div>
				<div class="btn-group">
					<button id="b_delete_product" type="button" class="btn btn-danger">Delete Product</button>
					<div class="" id="message_status_delete" style="display:none; background-color: #e60e0e; font-size: 14px; font-weight: 500; color: white; padding: 6px 12px; border-radius: 4px;">This Post Already Deleted</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<!--<a id="" href="<?=base_url_admin('ecommerce/produk/edit/'.$produk->id); ?>" class="btn btn-info"><i class="fa fa-edit"></i> Edit</a>-->
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Produk</li>
		<li><?=$this->__st($produk->nama, 20)?></li>
	</ul>
	<input type="hidden" id="param_video_toggle" value="<?=$produk->product_type?>" />
	<input type="hidden" id="product_id" value="<?=$produk->id?>" />
	<input type="hidden" id="status_is_active" value="<?=$produk->is_active?>" />
	<input type="hidden" id="status_is_published" value="<?=$produk->is_published?>" />
	<input type="hidden" id="status_is_visible" value="<?=$produk->is_visible?>" />
	<input type="hidden" id="status_is_takedown" value="<?=$produk->reported_status?>" />
	<!-- END Static Layout Header -->

	<!-- product information -->
	<div class="block block-full">
		<div class="block-title">
			<h2><i class="fa fa-file-text-o"></i> <strong>Product Information</strong></h2>
		</div>
		<div class="row">
			<div class="col-md-6">
				<table class="table table-borderless table-striped">
					<tr>
						<th class="col-md-4">Product Name</th>
						<td class="col-md-1">:</td>
						<td><?=$produk->nama?></td>
					</tr>
					<tr>
						<th>Price</th>
						<td>:</td>
						<td><?=$produk->harga_jual?></td>
					</tr>
					<tr>
						<th>Category</th>
						<td>:</td>
						<td><?=$kategori->nama?></td>
					</tr>

					<!-- by Donny Dennison - 8 february 2021 16:44
        				add product type column in product menu -->
					<tr>
						<th>Product Type</th>
						<td>:</td>
						<td><?=$produk->product_type?></td>
					</tr>

					<tr>
						<th>Condition</th>
						<td>:</td>
						<td><?php if (ISSET($kondisi->nama)) {
							echo $kondisi->nama;
						} ?></td>
					</tr>
					<tr>
						<th>Created Date</th>
						<td>:</td>
						<td><?=date("j F y H:i", strtotime($produk->cdate))?></td>
					</tr>
					<tr>
						<th>Status</th>
						<td>:</td>
						<td><?php if ($produk->is_active==1) {
							if ($produk->is_published==1) {
								echo 'Published';
							} else {
								echo 'Draft';
							}
						} else {
							echo 'Inactive';
						} ?></td>
					</tr>
					<!-- by Muhammad Sofi 2 March 2022 22:00 | request by Mr. Jackie add link product detail and seller(shop) link -->
					<tr>
						<th>Seller Shop Link</th>
						<td>:</td>
						<td>https://sellon.net/shop/<?=$user->id;?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="table table-borderless table-striped">
					<tr>
						<th class="col-md-4">Stock</th>
						<td class="col-md-1">:</td>
						<td><?=$produk->stok?> <?=$produk->satuan?></td>
					</tr>
					<tr>
						<th>Include Delivery Cost</th>
						<td>:</td>
						<td><?php if ($produk->is_include_delivery_cost==1) {
							echo 'Yes';
						} else {
							echo 'No';
						}?></td>
					</tr>
					<tr>
						<th>Show on Homepage</th>
						<td>:</td>
						<td><?php if ($produk->is_featured==1) {
							echo 'Yes';
						} else {
							echo 'No';
						}?></td>
					</tr>
					<!-- by Muhammad Sofi - 18 November 2021 12:00 -->
					<tr>
						<th>Brand</th>
						<td>:</td>
						<td><?=$produk->brand?></td>
					</tr>
					<tr>
						<th>Model</th>
						<td>:</td>
						<td><?=$produk->detail_automotive->model?></td>
					</tr>
					<tr>
						<th>Color</th>
						<td>:</td>
						<td><?=$produk->detail_automotive->color?></td>
					</tr>
					<tr>
						<th>Year</th>
						<td>:</td>
						<td><?=$produk->detail_automotive->year?></td>
					</tr>
					<!-- by Muhammad Sofi 2 March 2022 22:00 | request by Mr. Jackie add link product detail and seller(shop) link -->
					<tr>
						<th>Product Detail Link</th>
						<td>:</td>
						<td>https://sellon.net/product_detail/<?=$produk->id?></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h4><strong>Product Description</strong></h4>
				<div class="std">
					<?=$this->__format($produk->deskripsi, 'richtext')?>
				</div>
			</div>
		</div>
	</div>
	<!-- end product information -->

	<!-- product images-->
	<div class="block block-full">
		<div class="block-title">
			<h2><strong>Product Image(s)</strong></h2>
		</div>
		<div class="block-section text-center">
			<div class="row">
				<?php foreach ($produk->fotos as $foto) { ?>
				<div class="col-md-2 mp1">
					<div class="bordered">
						<a href="<?=base_url($foto->url); ?>" target="_blank" title="View Detail">
							<img src="<?=base_url($foto->url); ?>" class="img-responsive" />
						</a>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<!-- end product images-->

	<!-- seller properties -->
	<div class="row">
		<!-- row left -->
		<div class="col-md-6">
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-user-o"></i> <strong>Seller Info</strong> </h2>
				</div>
				<div class="row">
					<div class="col-md-3">&nbsp;</div>
					<div class="col-md-6">
						<img src="<?=base_url($user->image)?>" class="img-responsive" style="width:50%" onerror="this.onerror=null;this.src='<?=base_url()?>media/default.png';" />
					</div>
					<div class="col-md-3">&nbsp;</div>
				</div>
        		<table class="table  table-striped  table-borderless">
					<tr>
						<th>Seller Name</th>
						<td>:</td>
						<td><?=$user->fnama; ?></td>
					</tr>
					<tr>
						<th>Seller Email</th>
						<td>:</td>
						<td><a class="text_link" href="mailto:<?=$user->email;?>" target="_blank"><?=$user->email?></a></td>
					</tr>
					<tr>
						<th>Seller Phone</th>
						<td>:</td>
						<td>
							<?php if(isset($user->telp)) { ?>
								<a class="text_link" href="https://api.whatsapp.com/send?phone=<?=$user->nation_code.$user->telp;?>" target="_blank">
									<?=$user->telp;?>
								</a>
							<?php } else { ?>
							-
							<?php } ?>
						</td>
					</tr>
        		</table>
      		</div>
		</div>
		<!-- end row left -->

		<!-- row right -->
		<div class="col-md-6">
			<!-- Pickup Address -->
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-map-o"></i> <strong>Pickup Address</strong> </h2>
				</div>
				<?php if (isset($alamat->penerima_nama)) { ?>
					<address>
						<h4><strong><?=$alamat->penerima_nama; ?> <small style="display:none;"><a href="https://api.whatsapp.com/send?phone=<?=$alamat->penerima_telp; ?>" target="_blank"><i class="fa fa-whatsapp"></i> <?=$alamat->penerima_nama; ?></a></small></strong></h4>
						<address>
							<?=str_replace(" ".$alamat->kodepos, "", $alamat->alamat2)?>
							<br />
							<?=$alamat->catatan?>
							<br />
							<?=$alamat->negara.' '.$alamat->kodepos; ?><br>
							<br />
						</address>
					</address>
				<?php } else { ?>
					<p style="font-weight: 100; font-style: italic;">Pickup address not set</p>
				<?php } ?>
				<table class="table table-striped table-borderless">
					<tr>
						<th>Courier Service</th>
						<td>:</td>
						<td><?=ucwords($produk->courier_services ? $produk->courier_services : "-")?></td>
					</tr>
					<tr>
						<th>Shipment Vehicle</th>
						<td>:</td>
						<td><?=ucwords($produk->vehicle_types ? $produk->vehicle_types : "-")?></td>
					</tr>
					<tr>
						<th>Weight</th>
						<td>:</td>
						<td><?=$produk->berat?> KG</td>
					</tr>
					<tr>
						<!-- By Donny Dennison - 27 july 2020 14:24 -->
						<!-- change length to depth -->
						<!-- <th>Dimension (LxWxH)</th> -->
						<th>Dimension (WxDxH)</th>
						<td>:</td>
						<!-- By Donny Dennison - 27 july 2020 14:24 -->
						<!-- change length to depth -->
						<!-- <td><?=$produk->dimension_long.' x '.$produk->dimension_width.' x '.$produk->dimension_height?> CM</td> -->
						<td><?=$produk->dimension_width.' x '.$produk->dimension_long.' x '.$produk->dimension_height?> CM</td>
					</tr>
				</table>
			</div>
			<!-- End Pickup Address -->
		</div>
		<!-- end row right -->
	</div>
	<!-- end seller properties -->

	<div class="row">
		<!-- row left -->
		<div class="col-md-6">
			<div class="block">
				<!-- Account Status Title -->
				<div class="block-title">
					<h2><i class="fa fa-star-o" aria-hidden="true"></i> <strong>Seller Offer Review</strong></h2>
				</div>
				<!-- END Account Status Title -->

				<!-- Account Stats Content -->
				<table class="table table-striped table-borderless">
					<tbody>
						<tr>
							<th class="text-center" style="background-color: #0c76f0; color: #fff; border-radius: 15px">Total Seller Rating</th>
						</tr>
						<tr>
							<th style="width: 180px;">Average Rating</th>
							<td>:</td>
							<td style="color: #DD8A0D"> 
								<?php if(isset($user->offer_rating_seller_avg)) {
									$rating = round($user->offer_rating_seller_avg);
									echo $this->__toStars($rating);
								} ?>
							</td>
						</tr>
						<tr>
							<th style="width: 180px;">Total Rating</th>
							<td>:</td>
							<td><?= $user->offer_rating_seller_total; ?></td>
						</tr>
					</tbody>
				</table>
				<?php if($seller_review) { ?>
					<div class="row">
						<div class="col-md-12">
							<div style="border-bottom: 1px solid #6b6a6a; border-bottom-width: thin; margin-top: -10px; margin-bottom: 10px;"></div>
						</div>
					</div>
					<?php foreach($seller_review as $review) {  ?>
						<table class="table table-striped table-borderless">
							<tbody>
								<tr>
									<th style="width: 100px;">Buyer Name</th>
									<td>:</td>
									<td><strong><?=$review->buyer_name?></strong></td>
								</tr>
								<tr>
									<th style="width: 100px;">Review</th>
									<td>:</td>
									<td><strong><?=$review->review ? $review->review : "-" ?></strong></td>
								</tr>
								<tr>
									<th style="width: 100px;">Star</th>
									<td>:</td>
									<td style="color: #DD8A0D">
										<?php $rating = 0; 
											if (isset($review->star)) {
												$rating = $review->star;
											} 
											echo $this->__toStars($rating);
										?>
									</td>
								</tr>
							</tbody>
						</table>
						<hr />
					<?php }?>
				<?php } else { ?>
					<p class="text-center">there is no review for this product.</p>
				<?php } ?>
			</div>
		</div>
		<!-- end row left -->

		<!-- row right -->
		<div class="col-md-6"></div>
		<!-- end row right -->
	</div>

</div>
