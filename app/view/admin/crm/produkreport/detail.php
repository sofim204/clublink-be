<style>
	.bordered {
		border: 1px #ededed solid;
	}
	.mp1 {
		padding: 1em;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
					<a id="" href="<?=base_url_admin('crm/produkreport/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<!--<a id="" href="<?=base_url_admin('crm/produkreport/edit/'.$produk->id); ?>" class="btn btn-info"><i class="fa fa-edit"></i> Edit</a>-->
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Produk Reports</li>
		<li><?=$this->__st($produk->nama,20)?></li>
	</ul>
	<!-- END Static Layout Header -->

	<div class="block block-full">
		<div class="block-title">
			<h2><strong>Report Action</strong></h2>
		</div>
		<div class="row">
      		<div class="col-md-12">
        		<div class="btn-group pull-right">
					<?php if($produk->reported_status == "takedown"){ ?>
						<a id="btakedown" href="#" class="btn btn-warning btn-alt" disabled><i class="fa fa-arrow-down"></i> Takedown</a>
					<?php } else { ?>
						<a id="btakedown" href="#" class="btn btn-warning btn-alt"><i class="fa fa-arrow-down"></i> Takedown</a>
					<?php } ?>
					<?php if($produk->reported_status == "ignore"){ ?>
						<a id="bignore" href="#" class="btn btn-warning btn-alt" disabled><i class="fa fa-hand-paper-o"></i> Ignore</a>
					<?php } else { ?>
						<a id="bignore" href="#" class="btn btn-warning btn-alt"><i class="fa fa-hand-paper-o"></i> Ignore</a>
					<?php } ?>
        		</div>
     		 </div>
      		<div class="col-md-12">&nbsp;</div>
   		 </div>
	</div>
	<div class="block block-full">
		<div class="block-title">
			<h2><strong>Reports</strong></h2>
		</div>
		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr>
						<th>Reporter</th>
						<th>Category</th>
						<th>Sub Category</th>
						<th>Description</th>

						<!-- //by Donny Dennison - 2 march 2021 10:52 -->
        				<!-- //add need action column in dashboard -->
						<th>Action</th>

					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<br />
	</div>

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
						<td>$<?=$produk->harga_jual?></td>
					</tr>
					<tr>
						<th>Category</th>
						<td>:</td>
						<td><?=$kategori->nama?></td>
					</tr>
					<tr>
						<th>Condition</th>
						<td>:</td>
						<td><?=$kondisi->nama?></td>
					</tr>
					<tr>
						<th>Created Date</th>
						<td>:</td>
						<td><?=date("j F y H:i",strtotime($produk->cdate))?></td>
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
						<td><?php if($produk->is_include_delivery_cost==1){ echo 'Yes'; }else{ echo 'No'; }?></td>
					</tr>
					<tr>
						<th>Show on Homepage</th>
						<td>:</td>
						<td><?php if($produk->is_featured==1){ echo 'Yes'; }else{ echo 'No'; }?></td>
					</tr>
					<tr>
						<th>Status</th>
						<td>:</td>
						<td><?php if($produk->is_active==1){
							if($produk->is_published==1){
								echo 'Published';
							}else{
								echo 'Draft';
							}
						}else{
							echo 'Inactive';
						} ?></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h3>Product Description</h3>
				<div class="std">
					<?=$this->__format($produk->deskripsi,'richtext')?>
				</div>
				<br />
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
				<?php foreach($produk->fotos as $foto){ ?>
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
            <img src="<?=base_url($user->image)?>" class="img-responsive" onerror="this.onerror=null;this.src='<?=base_url()?>media/default.png';" />
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
            <td><a href="mailto:<?=$user->email;?>" target="_blank"><?=$user->email?></a></td>
          </tr>
          <tr>
            <th>Seller Phone</th>
            <td>:</td>
            <td><a href="https://api.whatsapp.com/send?phone=<?=$user->nation_code.$user->telp;?>" target="_blank"><?=$user->telp;?></a></td>
          </tr>
          <tr>
            <th>Seller Rating</th>
            <td>:</td>
            <td> <?php $rating = 0; if(isset($user->rating)) $rating = $user->rating; echo $this->__toStars($rating) ?></td>
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
				<?php if(isset($alamat->penerima_nama)){ ?>
				<address>
					<h4><strong><?=$alamat->penerima_nama; ?> <small><a href="https://api.whatsapp.com/send?phone=<?=$user->nation_code.$alamat->penerima_telp; ?>" target="_blank"><i class="fa fa-whatsapp"></i> <?=$alamat->penerima_nama; ?></a></small></strong></h4>
					<address>
					<!-- 
						by Muhammad Sofi - 3 November 2021 10:00
					 	remark code -->
						 <!-- by Muhammad Sofi 18 January 2022 17:18 | delete unused code(cause page does not load properly) -->
						<?=$alamat->alamat2; ?><br>
						<?=$alamat->kecamatan.', '.$alamat->kabkota; ?><br>
						<?=$alamat->provinsi.', '.$alamat->negara.' '.$alamat->kodepos; ?><br><br>
					</address>
				</address>
				<?php }else{ ?>
				<p style="font-weight: 100; font-style: italic;">Pickup address not set</p>
				<?php } ?>
				<table class="table table-striped table-borderless">
					<tr>
						<th>Courier Service</th>
						<td>:</td>
						<td><?=ucwords($produk->courier_services)?></td>
					</tr>
					<tr>
						<th>Shipment Vehicle</th>
						<td>:</td>
						<td><?=ucwords($produk->vehicle_types)?></td>
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

</div>
