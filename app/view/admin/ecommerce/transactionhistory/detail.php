<?php
	//$is_very_super_admin=1;
	$d = $order->detail;

	if (!isset($is_very_super_admin)) {
		$is_very_super_admin=0;
	}

	if (!isset($is_advanced)) {
		$is_advanced=0;
	}

	$c_produk_id = $order->detail->id;
	$d_order_detail_id = $order->detail->id;

	//calculation commision
	$sc = $order->detail->shipment_cost + $order->detail->shipment_cost_add;
	$sc2 = $order->detail->shipment_cost_sub;

	if (!isset($order->shipping->address_notes)) {
		$order->shipping->address_notes = '';
		if (isset($order->shipping->catatan)) {
			$order->shipping->address_notes = $order->shipping->catatan;
		}
	}

	if (!isset($order->pickup->address_notes)) {
		$order->pickup->address_notes = '';
		if (isset($order->pickup->catatan)) {
			$order->pickup->address_notes = $order->pickup->catatan;
		}
	}

	$shipping = $order->shipping;
	$pickup = $order->pickup;

	//kirim alamat (shipping)

	// by Donny Dennison - 3 November 2021 10:00
	// remark code
	// $ka = $this->__addressStructureFixer($shipping->alamat, $shipping->alamat2, $shipping->address_notes, $shipping->negara, $shipping->kodepos);
	$ka = $this->__addressStructureFixer($shipping->alamat2, $shipping->address_notes, $shipping->negara, $shipping->kodepos);

	//pickup

	// by Donny Dennison - 3 November 2021 10:00
	// remark code
	// $pa = $this->__addressStructureFixer($pickup->alamat, $pickup->alamat2, $pickup->address_notes, $pickup->negara, $pickup->kodepos);
	$pa = $this->__addressStructureFixer($pickup->alamat2, $pickup->address_notes, $pickup->negara, $pickup->kodepos);

	// START by Muhammad Sofi 8 February 2022 13:58 | add check if chat room id is empty, hide button -->
	if(!isset($room_chat_data->e_chat_room_id)) {
		$room_chat_all = 0;
	} else {
		$room_chat_all = $room_chat_data->e_chat_room_id;
	}

	if(!isset($room_chat_admin_seller->room_chat_id)) {
		$room_admin_seller = 0;
	} else {
		$room_admin_seller = $room_chat_admin_seller->room_chat_id;
	}

	if(!isset($room_chat_admin_buyer->room_chat_id)) {
		$room_admin_buyer = 0;
	} else {
		$room_admin_buyer = $room_chat_admin_buyer->room_chat_id;
	}
	// END by Muhammad Sofi 8 February 2022 13:58 | add check if chat room id is empty, hide button -->
?>
<style>
	.account-content {
		padding: 20px;
	}
	.account-content {
		border: 1px #e5e5e5 solid;
		background-color: #fff;
		margin-bottom: 1em;
	}
	.account-content .checkout-step {
		position: relative;
		text-align: center;
		margin-bottom: 15px;
	}
	.account-content .checkout-step .number {
		background-color: #F0F0F0;
		color: #AAAAAA;
		border-radius: 50%;
		display: inline-block;
		height: 29px;
		width: 29px;
		font-weight: bold;
		font-size: 20px;
	}
	.account-content .checkout-step.active .number {
		color: #FFFFFF;
	}
	.account-content .checkout-step.active .number, .account-content .checkout-progress .progress-bar, .brands .item a:before {
		background-color: #1bbae1;
	}
	.thmb {
		border: 1px solid #e7e7e7;
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
		border-radius: 3px;
		padding: 10px;
		margin-bottom: 20px;
		position: relative;
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
		color:#FFF;
	}
</style>

<div id="page-content">
	<!-- by Muhammad Sofi 8 February 2022 13:58 | add back button -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a href="<?=base_url_admin('ecommerce/transactionhistory/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Ecommerce</li>
		<li><a href="<?=base_url_admin('ecommerce/transactionhistory/')?>">Transaction History</a></li>
		<li>Detail</li>
		<li>ORDER ID: #<?=$order->id?></li>
		<li>PRODUCT ID: #<?=$order->detail->id?></li>
		<!-- by Muhammad Sofi 8 February 2022 13:58 | add check if chat room id is empty, hide button -->
		<input type="hidden" id="value_room_chat_all" value="<?=$room_chat_all?>" />
		<input type="hidden" id="value_room_admin_seller" value="<?=$room_admin_seller?>" />
		<input type="hidden" id="value_room_admin_buyer" value="<?=$room_admin_buyer?>" />
	</ul>

	<!-- order info -->
	<div class="block">
		<div class="block-title">
			<h2><i class="fa fa-file-text-o"></i> <strong>Order Information</strong></h2>
		</div>

		<div class="row">
			<!--left-->
			<div class="col-md-6">
			<table class="table table-striped table-borderless">
				<tr>
					<th>Invoice</th>
					<td>:</td>
					<td><?=$order->invoice_code?></td>
				</tr>
				<tr>
					<th>Order Date</th>
					<td>:</td>
					<td><?=$order->cdate?></td>
				</tr>
				<tr>
					<th>Order Status</th>
					<td>:</td>
					<td><?=$this->__orderStatusText($order->order_status)?></td>
				</tr>
				<tr>
					<th>Payment Status</th>
					<td>:</td>
					<td><?=$this->__paymentStatusText($order->payment_status)?></td>
				</tr>
				<tr>
					<th>Seller Status</th>
					<td>:</td>
					<td><?=$st->seller?></td>
				</tr>
				<tr>
					<th>Buyer Status</th>
					<td>:</td>
					<td><?=$st->buyer?></td>
				</tr>
			</table>
			</div>
			<!--end left-->

			<!--right-->
			<div class="col-md-6">
				<table class="table  table-striped  table-borderless">
					<tr>
						<th>Payment Gateway</th>
						<td>:</td>
						<td><?=$order->payment_gateway?></td>
					</tr>
					<tr>
						<th>Payment TranID</th>
						<td>:</td>
						<td><?=$order->payment_tranid?></td>
					</tr>
					<tr>
						<th>Payment Date</th>
						<td>:</td>
						<td><?=$order->payment_date?></td>
					</tr>
					<tr>
						<th>Order Buyer Rating</th>
						<td>:</td>
						<td> <?php $rating_value = 0; if (isset($rating->buyer_rating)) {
							$rating_value = $rating->buyer_rating;
						} echo $this->__toStars($rating_value) ?></td>
					</tr>
					<tr>
						<th>Order Seller Rating</th>
						<td>:</td>
						<td> <?php $rating_value = 0; if (isset($rating->seller_rating)) {
								$rating_value = $rating->seller_rating;
							} echo $this->__toStars($rating_value) ?></td>
					</tr>
					<tr>
						<th>Distance</th>
						<td>:</td>
						<td><?=ceil($order->detail->shipment_distance/1000)?>KM</td>
					</tr>
				</table>
			</div>
			<!--end right-->
		</div>
	</div>
	<!-- end order info -->

	<!-- buttons -->
	<div class="block">
		<div class="block-title">
			<h2><i class="fa fa-file-text-o"></i> <strong>Order Action</strong></h2>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="btn-group pull-right">
					<a href="<?=base_url_admin("ecommerce/transaction/buyer_detail/$order->id/")?>" class="btn btn-default btn-alt"><i class="fa fa-info-circle"></i> Transaction by Buyer</a>
					<a href="<?=base_url_admin("ecommerce/waybill/download/$order->id/$d_order_detail_id/")?>" class="btn btn-default btn-alt"><i class="fa fa-print"></i> Waybill</a>
					<!-- by Muhammad Sofi 3 February 2022 13:20 | table e_chat_v2 is not used -->

					<!-- START by Muhammad Sofi 24 January 2022 15:00 | remove chat_v2 and restore button Open Chat -->
					<a id="btn_open_chat_all" href="<?=base_url_admin("crm/chat/detail/buyandsell/$room_chat_all")?>" class="btn btn-default btn-alt"><i class="fa fa-wechat"></i> Open Chat All</a>
					<a id="btn_chat_seller" href="<?=base_url_admin("crm/chat/detail/admin/$room_admin_seller");?>" class="btn btn-default btn-alt"><i class="fa fa-wechat"></i> Chat With <?=$seller->fnama; ?></a>
					<a id="btn_chat_buyer" href="<?=base_url_admin("crm/chat/detail/admin/$room_admin_buyer");?>" class="btn btn-default btn-alt"><i class="fa fa-wechat"></i> Chat With <?=$buyer->fnama; ?></a>
					<!-- END by Muhammad Sofi 24 January 2022 15:00 | remove chat_v2 and restore button Open Chat -->
				</div>
			</div>
			<div class="col-md-12">&nbsp;</div>
		</div>
	</div>
	<!-- end buttons -->

	<?php if (isset($complain->nation_code)) { ?>
		<!-- complain block -->
		<div class="block">
			<div class="block-title">
				<h2><i class="fa fa-exclamation-triangle text-danger"></i> <strong>Complain</strong></h2>
			</div>

			<div class="row">
				<div class="col-md-12">
				<blockquote cite="<?=base_url_admin("crm/chat/detail/$order->id/$d_order_detail_id/")?>">
					<?php $this->__e($complain->alasan); ?>
					<small>from <?=$complain->dari?></small>
				</blockquote>
				</div>
			</div>
		</div>
		<!-- end complain block -->
	<?php  } ?>

	<!-- Products Block -->
	<div class="block">
		<!-- Products Title -->
		<div class="block-title">
			<h2><i class="fa fa-shopping-cart"></i> <strong>Product(s) and Settlement</strong></h2>
		</div>
		<!-- END Products Title -->

		<!-- Products Content -->
		<div class="table-responsive">
			<table id="produk_tabel" class="table table-bordered table-vcenter">
				<thead>
					<tr>
						<th class="" colspan="2">Product</th>
						<th class="text-center">Stock</th>
						<th class="text-center">QTY</th>
						<th class="text-center" >Price</th>
						<th class="text-center">Total</th>
					</tr>
				</thead>
				<tbody>
					<?php $stok = 0; $sub_total = 0.0; foreach ($items as $ods) { ?>
					<?php $stok += $ods->stok; $sub_total += $ods->qty*$ods->harga_jual; ?>
					<tr>
						<td class="col-md-1 text-center">
							<img src="<?=base_url($ods->foto)?>" class="img-responsive" onerror="this.onerror=null;this.src='<?=base_url()?>media/produk/default.png';" />
						</td>
						<td class="">
							<h4 class="" style="font-weight: bolder;margin-bottom: 0.25em;">
								<?=$ods->nama; ?>
							</h4>
							<?php if ($ods->buyer_status == "rejected" && $ods->settlement_status == "wait") { ?>
							<p style="font-size: smaller;margin-bottom: 0;" class=" text-danger"><a href="<?=base_url_admin("ecommerce/rejectbuyer/?keyword=".$order->invoice_code)?>" target="_blank" style="color: red; text-decoration: underline;" title="view rejected item"><i class="fa fa-info-circle"></i> Rejected by buyer, waiting for admin action</a></p>
							<?php } ?>
							<?php if ($ods->buyer_status == "rejected" && $ods->settlement_status == "paid_to_buyer") { ?>
							<p style="font-size: smaller;margin-bottom: 0;" class=" text-info"><i class="fa fa-info-circle"></i> Rejected by buyer, approved by admin</p>
							<?php } ?>
						</td>
						<td class="text-center"><strong><?=$this->__n($ods->stok, 'Pcs', '1'); ?></strong></td>
						<td class="text-center"><strong><?=$this->__n($ods->qty, 'Pcs', '1'); ?></strong></td>
						<td class="text-right"><strong><?=$this->__m($ods->harga_jual*$ods->qty, 0, ',', '.'); ?></strong></td>
						<td class="text-right"><strong><?=$this->__m(($ods->harga_jual*$ods->qty), 0, ',', '.'); ?></td>
					</tr>
					<?php } ?>
					<tr class="active">
						<td colspan="5" class="text-right text-uppercase"><strong>Sub total:</strong></td>
						<td class="text-right"><strong><?=$this->__m($sub_total, 0, ',', '.'); ?></strong></td>
					</tr>
					<tr>
						<td colspan="5" class="text-right text-uppercase"><strong>Shipping Cost (<?=$order->detail->shipment_service.': '.$order->detail->shipment_type?>):</strong></td>
						<td class="text-right"><strong><?=$this->__m($sc, 0, ',', '.'); ?></strong></td>
					</tr>
					<?php if ($sc2>0) { ?>
					<tr>
						<td colspan="5" class="text-right text-uppercase text-warning"><strong>Shipping Cost Paid By Seller:</strong></td>
						<td class="text-right text-warning"><strong><?=$this->__m($sc2, 0, ',', '.'); ?></strong></td>
					</tr>
					<?php } ?>
					<tr class="active">
						<td colspan="5" class="text-right text-uppercase"><strong>Total:</strong></td>
						<td class="text-right"><strong><?=$this->__m($sub_total+$order->detail->shipment_cost+$order->detail->shipment_cost_add, 0, ',', '.'); ?></strong></td>
					</tr>
					<tr class="">
						<td colspan="5" class="text-right text-uppercase text-info"><strong>Selling Fee:</strong></td>
						<td class="text-right text-info"><strong><?=$this->__m($d->selling_fee); ?></strong></td>
					</tr>
					<tr class="active">
						<td colspan="5" class="text-right text-uppercase text-danger"><strong>Refund Amount:</strong></td>
						<td class="text-right text-danger"><strong><?=$this->__m($d->refund_amount); ?></strong></td>
					</tr>
					<tr class="">
						<td colspan="5" class="text-right text-uppercase text-success"><strong>Seller Earning:</strong></td>
						<td class="text-right  text-success"><strong><?=$this->__m($d->earning_total); ?></strong></td>
					</tr>
				</tbody>
			</table>
		</div>
		<!-- END Products Content -->
	</div>
	<!-- END Products Block -->

	<!-- Buyer and Seller Row -->
	<div class="row">

		<!-- end col left -->
		<div class="col-sm-6">

			<!-- Seller Info -->
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-user-o"></i> <strong>Seller Info</strong> </h2>
				</div>
				<div class="row">
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">
						<img src="<?=base_url($seller->image)?>" class="img-responsive" onerror="this.onerror=null;this.src='<?=base_url()?>media/default.png';" />
					</div>
					<div class="col-md-4">&nbsp;</div>
				</div>
				<table class="table  table-striped  table-borderless">
					<tr>
						<th>Seller Name</th>
						<td>:</td>
						<td><?=$seller->fnama; ?></td>
					</tr>
					<tr>
						<th>Seller Email</th>
						<td>:</td>
						<td><a href="mailto:<?=$seller->email;?>" target="_blank"><?=$seller->email?></a></td>
					</tr>
					<tr>
						<th>Seller Phone</th>
						<td>:</td>

						<!-- by Donny Dennison - 28 august 2020 16:39 -->
						<!-- <td><a href="https://api.whatsapp.com/send?phone=<?=$nation_code.$seller->telp;?>" target="_blank" style><?=$seller->telp;?></a></td> -->
						<td><?=$seller->telp;?></td>
						
					</tr>
					<tr>
						<th>Seller Rating</th>
						<td>:</td>
						<td> <?php $rating_value = 0; if (isset($seller->rating)) {
							$rating_value = $seller->rating;
						} echo $this->__toStars($rating_value) ?></td>
					</tr>
				</table>
			</div>
			<!-- END Seller Info -->

			<!-- Seller Bank Account -->
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-map"></i> <strong>Seller</strong> Bank Account </h2>
				</div>
				<table class="table table-striped table-borderless">
					<tr>
						<th>Account Name</th>
						<td>:</td>
						<td><?php if (isset($bank_seller->nama)) { echo $bank_seller->nama; }?></td>
					</tr>
					<tr>
						<th>Bank Name</th>
						<td>:</td>
						<td><?php if (isset($bank_seller->nama_bank)) { echo $bank_seller->nama_bank; }?></td>
					</tr>
					<tr>
						<th>Account Number</th>
						<td>:</td>
						<td><?php if (isset($bank_seller->nomor)) { echo $bank_seller->nomor; }?></td>
					</tr>
				</table>
			</div>
			<!-- End Seller Bank Account -->

			<!-- Pickup Address -->
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-map-o"></i> <strong>Pickup Address</strong> </h2>
				</div>
				<address>
					<h4><strong><?=$order->pickup->penerima_nama; ?> <small style="display:none;"><a href="https://api.whatsapp.com/send?phone=<?=$order->nation_code.$order->pickup->penerima_telp; ?>" target="_blank"><i class="fa fa-whatsapp"></i> <?=$order->pickup->penerima_nama; ?></a></small></strong></h4>
					<address>
						<?=$pa[0]?>
						<br>
						<?=$pa[1]?>
						<br>
						<?=$pa[2]?>
						<br>
						<?=$pa[3]?>
						<br>
					</address>
				</address>
				<table class="table table-striped table-borderless">
					<tr>
						<th>Shipment Service</th>
						<td>:</td>
						<td><?=$order->detail->shipment_service?></td>
					</tr>
					<tr>
						<th>Shipment Type</th>
						<td>:</td>
						<td><?=$order->detail->shipment_type?></td>
					</tr>
					<tr>
						<th>Shipment Vehicle</th>
						<td>:</td>
						<td><?=$order->detail->shipment_vehicle?></td>
					</tr>
				</table>
			</div>
			<!-- End Pickup Address -->

		</div>
		<!-- end col left -->

		<!-- col right -->
		<div class="col-sm-6">

			<!-- Buyer Info -->
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-user"></i> <strong>Buyer Info</strong> </h2>
				</div>
				<div class="row">
					<div class="col-md-4">&nbsp;</div>
					<div class="col-md-4">
						<img src="<?=base_url($buyer->image)?>" class="img-responsive"  onerror="this.onerror=null;this.src='<?=base_url()?>media/default.png';" />
					</div>
					<div class="col-md-4">&nbsp;</div>
				</div>
				<table class="table  table-striped  table-borderless">
					<tr>
						<th>Buyer Name</th>
						<td>:</td>
						<td><?=$buyer->fnama; ?></td>
					</tr>
					<tr>
						<th>Buyer Email</th>
						<td>:</td>
						<td><a href="mailto:<?=$buyer->email;?>" target="_blank"><?=$buyer->email?></a></td>
					</tr>
					<tr>
						<th>Buyer Phone</th>
						<td>:</td>
						<td>
						<!--<a href="https://api.whatsapp.com/send?phone=<?=$nation_code.$buyer->telp;?>" target="_blank"><?=$buyer->telp;?></a>-->
						<?=$buyer->telp;?>
						</td>
					</tr>
					<tr>
						<th>Buyer Rating</th>
						<td>:</td>
						<td> <?php $rating_value = 0; if (isset($buyer->rating)) {
							$rating_value = $buyer->rating;
						} echo $this->__toStars($rating_value) ?></td>
					</tr>
				</table>
			</div>
			<!-- END Buyer Info -->

			<!-- Buyer Bank Account -->
			<div class="block">
				<div class="block-title">
					<h2><i class="fa fa-map"></i> <strong>Buyer</strong> Bank Account </h2>
				</div>
				<?php if (!empty($bank_buyer->nama)) { ?>
				<table class="table table-striped table-borderless">
					<tr>
						<th>Account Name</th>
						<td>:</td>
						<td><?=$bank_buyer->nama?></td>
					</tr>
					<tr>
						<th>Bank Name</th>
						<td>:</td>
						<td><?=$bank_buyer->nama_bank?></td>
					</tr>
					<tr>
						<th>Account Number</th>
						<td>:</td>
						<td><?=$bank_buyer->nomor?></td>
					</tr>
				</table>
				<?php } else { ?>
				<p>This user hasn't a bank account yet</p>
				<?php } ?>
			</div>
			<!-- End Buyer Bank Account -->

			<!-- Shipping Address -->
			<div class="block">
				<div class="block-title">

					<!-- by Donny Dennison - 9 august 2020 - 18:11 -->
					<!-- change shipping address to receiving address -->
					<!-- <h2><i class="fa fa-map"></i> <strong>Shipping Address</strong> </h2> -->
					<h2><i class="fa fa-map"></i> <strong>Receiving Address</strong> </h2>

				</div>
				<address>
					<h4><strong><?=$order->shipping->nama; ?> <small style="display: none;"><a href="https://api.whatsapp.com/send?phone=<?=$order->nation_code.$order->shipping->telp; ?>" target="_blank"><i class="fa fa-whatsapp"></i> <?=$order->shipping->nama; ?></a></small></strong></h4>
					<address>
						<?=$ka[0]?>
						<br>
						<?=$ka[1]?>
						<br>
						<?=$ka[2]?>
						<br>
						<?=$ka[3]?>
						<br>
					</address>
				</address>
				<table class="table table-striped table-borderless">
					<tr>
						<th>Shipment Status</th>
						<td>:</td>
						<td><?php
						
						//By Donny Dennison - 08-07-2020 16:16
						//Request by Mr Jackie, add new shipment status "courier fail"
						if ($d->shipment_status == 'courier fail') {
							echo 'Courier Fail';

						} elseif ($d->shipment_status == 'delivered' && strlen($d->delivery_date) > 9 && strlen($d->received_date) > 9) {
							echo 'delivered';
						} elseif ($d->shipment_status == 'delivered' && strlen($d->delivery_date) >9 && strlen($d->received_date) <= 9) {
							echo 'delivery in progress';
						} elseif (($d->shipment_status == 'process' || $d->shipment_status == 'delivered') && strlen($d->delivery_date) <= 9) {
							echo 'not yet sent';
						} elseif ($d->shipment_status == 'succeed') {
							echo 'received';
						} else {
							echo $d->shipment_status;
						}
						?></td>
					</tr>
					<tr>
						<th>Shipment TranID</th>
						<td>:</td>
						<td><?=strlen($d->shipment_tranid)?$d->shipment_tranid:'-'?></td>
					</tr>
				</table>
			</div>
			<!-- End Shipping Address -->

		</div>
		<!-- end col right -->

		<!-- end col right -->
	</div>
	<!-- END Buyer and Seller Row -->

	<!-- Log Block -->
	<div class="block">
		<!-- Log Title -->
		<div class="block-title">
			<h2><i class="fa fa-file-text-o"></i> <strong>Order Process</strong></h2>
		</div>
		<!-- END Log Title -->

		<!-- Log Content -->
		<div class="table-responsive">
			<table class="table table-bordered table-vcenter">
			<tbody>
				<?php foreach ($order->proses as $proses) { ?>
				<tr>
					<td>
					<span class=" "><?=$proses->nama?></span>
					</td>
					<td class="text-center"><?=date("j F y H:i", strtotime($proses->cdate))?></td>
					<td><a href="javascript:void(0)"><?=$proses->initiator?></a></td>
					<td class="col-md-7 text-default"><?=$proses->deskripsi?></td>
				</tr>
				<?php } ?>

			</tbody>
			</table>
		</div>
		<!-- END Log Content -->
	</div>
	<!-- END Log Block -->
</div>