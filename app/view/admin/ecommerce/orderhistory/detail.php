<?php
//$is_very_super_admin=1;
//$is_very_super_admin=1;
if(!isset($is_very_super_admin)) $is_very_super_admin=0;
if(!isset($is_advanced)) $is_advanced=0;
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
  <?php if($order->order_status == 'order_konfirmasi') echo 'background-color: #777777'; ?>
  <?php if($order->order_status == 'order_batal') echo 'background-color: #ef9d93'; ?>
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
</style>
<div id="page-content">
  <ul class="breadcrumb breadcrumb-top">
    <li>Ecommerce</li>
    <li><a href="<?=base_url_admin('ecommerce/order/')?>">Order</a></li>
    <li>Detail</li>
    <li>#<?=$order->id?></li>
  </ul>

  <!-- Order Status -->
  <div class="row text-center">
    <div class="col-sm-12 col-lg-4">
      <div class="widget">
        <div class="widget-extra themed-background-success">
          <h4 class="widget-content-light"><strong>TranID #<?php echo $order->id; ?></strong></h4>
        </div>
        <div class=" widget-extra-full"><span class="h4 text-success animation-expandOpen"><?=$this->__orderStatus($order->order_status); ?></span></div>
      </div>
    </div>
    <div class="col-sm-12 col-lg-4">
      <div class="widget">
        <div class="widget-extra <?php if($order->order_status == 'order_konfirmasi_sudah'){ $t='themed-background-danger'; }else if($order->order_status == 'order_konfirmasi'){ $t='themed-background-muted'; } else { $t='themed-background-success'; } echo $t; ?>">
          <h4 class="widget-content-light">
            <i class="fa fa-money"></i> <strong>Payment</strong>
          </h4>
        </div>
        <div class="widget-extra-full">
          <span class="h4 <?php if($order->order_status == 'order_konfirmasi_sudah'){ $t='text-danger'; }else if($order->order_status == 'order_konfirmasi'){ $t='text-muted'; } else { $t='text-success'; }; echo $t; ?> animation-expandOpen">
            <?php
            if($order->order_status == 'order_konfirmasi_sudah'){
              echo '<a href="'.base_url_admin('ecommerce/order/verifikasi/'.$order->id).'" title="Klik untuk memproses orderan ini" style="text-decoration: underline;">Unverified</a>';
            }else if($order->order_status == 'order_konfirmasi'){
              echo '<a href="'.base_url_admin('ecommerce/order/konfirmasi/'.$order->id).'" title="Klik untuk konfirmasi manual pembayaran" style="font-weight: bold; text-decoration: underline;">Not yet Confirmed</a>';
            } else {
              $t = '';
              echo $t.'$'.number_format($order->grand_total,0,',','.').' via '.$pembayaran->kode.' <i class="fa fa-check"></i>';
            }
            ?>
          </span>
        </div>
      </div>
    </div>
    <div class="col-sm-12 col-lg-4">
      <div class="widget">
        <div class="widget-extra themed-background-info">
          <h4 class="widget-content-light">
            <i class="fa fa-cog"></i>
            <strong>Action</strong>
          </h4>
        </div>
        <div class="" style="padding: 0.7em;">
          <div class="btn-group">
            <div class="btn-group">
              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                Option
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu dropdown-custom" aria-labelledby="orderMenu1" role="menu">
                <?php if($order->order_status == 'order_konfirmasi' || !empty($is_very_super_admin)){ ?>
                <li><a href="<?=base_url_admin('ecommerce/order/konfirmasi/'.$order->id)?>">Manual Confirmaton of Payment</a></li>
                <?php } ?>

                <?php if($order->order_status == 'order_konfirmasi_sudah' || !empty($is_very_super_admin)){ ?>
                <li><a href="<?=base_url_admin('ecommerce/order/verifikasi/'.$order->id)?>">Payment Verification</a></li>
                <?php } ?>

                <?php if( ($order->order_status == 'order_pembelian' || !empty($is_very_super_admin)) && !empty($is_advanced)){ ?>
                <li><a id="acekstok_proses_btn" href="<?=base_url_admin('ecommerce/order/cektsok/'.$order->id)?>">Check Stock Proceed</a></li>
                <?php } ?>

                <?php if( ($order->order_status == 'order_cekstok' || !empty($is_very_super_admin)) && !empty($is_advanced)){ ?>
                <li><a href="<?=base_url_admin('ecommerce/order/purchasing/'.$order->id)?>">Purchaseing Proceed</a></li>
                <?php } ?>


                <?php if( ($order->order_status == 'order_pembelian' || $order->order_status == 'order_cekstok' || !empty($is_very_super_admin)) && !empty($is_advanced)){ ?>
                <li><a href="<?=base_url_admin('ecommerce/order/qc/'.$order->id)?>">Proceed to QC</a></li>
                <?php } ?>

                <?php if($order->order_status == 'order_cekstok' || $order->order_status == 'order_qc' || !empty($is_very_super_admin)){ ?>
                <li><a id="a_order_proses_packing_menu" href="<?=base_url_admin('ecommerce/order/packing/'.$order->id)?>">Proceed Packing</a></li>
                <?php } ?>

                <?php if($order->order_status == 'order_packing' || !empty($is_very_super_admin)){ ?>
                <li><a id="a_order_proses_kirim_menu" href="<?=base_url_admin('ecommerce/order/kirim/'.$order->id)?>">Proceed Shipping</a></li>
                <?php } ?>

                <?php if($order->order_status == 'order_selesai' || !empty($is_very_super_admin)){ ?>
								<?php if($order->rating_nilai==0 || $order->rating_nilai == "0"){ ?>
                <li><a href="#" id="arating_modal">Rating</a></li>
								<?php } ?>
                <li style="display:none;"><a href="<?=base_url_admin('ecommerce/order/retur/'.$order->id)?>">Ajukan Retur</a></li>
                <?php } ?>

                <?php if($order->order_status == 'order_retur' || !empty($is_very_super_admin)){ ?>
                <li><a href="<?=base_url_admin('ecommerce/order/retur_batal/'.$order->id)?>">Cancel Return</a></li>
                <?php } ?>

                <li role="separator" class="divider"></li>
                <li style="display:none;"><a id="aorder_reoder" href="<?=base_url_admin('ecommerce/order/reorder/'.$order->id)?>">Purchase again</a></li>
                <li class=""><a id="aorder_batalkan" href="<?=base_url_admin('ecommerce/order/batalkan/'.$order->id)?>" class=""><i class="fa fa-times"></i> Cancel Order</a></li>
              </ul>
            </div>
						<div class="btn-group">
							<button class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
								<i class="fa fa-print"></i> Print <span class="caret"></span>
							</button>
							<ul class="dropdown-menu dropdown-custom" style="min-width: 150px;">
								<li>
									<a href="<?=base_url_admin('ecommerce/order/print_packing_list/'.$order->id)?>">Packing List</a>
								</li>
								<li>
									<a href="<?=base_url_admin('ecommerce/order/print_faktur/'.$order->id)?>">Invoice</a>
								</li>
							</ul>
						</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END Order Status -->


  <!-- order steps -->
  <div class="account-content checkout-steps">
    <div class="row row-no-padding">

      <div class="col-xs-6 col-sm-3">
        <div class="checkout-step active " title="<?=$status_teks?>">
          <div class="number"><i class="fa fa-<?=$pembayaran_icon?>"></i></div>
          <div class="title">Payment</div>
        </div>
      </div>

      <div class="col-xs-6 col-sm-3">
        <div class="checkout-step active" title="<?=$status_teks?>">
          <div class="number"><i class="fa fa-<?=$proses_icon?>"></i></div>
          <div class="title">Processing</div>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3">
        <div class="checkout-step active" title="<?=$status_teks?>">
          <div class="number"><i class="fa fa-<?=$qc_icon?>"></i></div>
          <div class="title">QC/Packing</div>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3">
        <div class="checkout-step active" title="<?=$status_teks?>">
          <div class="number"><i class="fa fa-<?=$kirim_icon?>"></i></div>
          <div class="title">Done</div>
        </div>
      </div>
    </div>
    <div class="progress checkout-progress hidden-xs" title="<?=$status_teks?>">
      <div class="progress-bar" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width:<?=$progress_bar?>%;"></div>
    </div>
  </div>
  <!-- end order steps -->


  <!-- Products Block -->
  <div class="block">
    <!-- Products Title -->
    <div class="block-title">
      <h2><i class="fa fa-shopping-cart"></i> <strong>Product</strong></h2>
    </div>
    <!-- END Products Title -->

    <!-- Products Content -->
    <div class="table-responsive">
      <table class="table table-bordered table-vcenter">
        <thead>
          <tr>
            <th class="">Product</th>
            <th class="text-center">Stock</th>
            <th class="text-center">QTY</th>
            <th class="text-center" style="width: 10%;">Price</th>
            <th class="text-center" style="width: 10%;">Discount</th>
            <th class="text-center" style="width: 10%;">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($order->detail as $ods){ ?>
          <?php
            $pp = '<strong>'.$ods->sku.'</strong> ';
            if(!empty($ods->ukuran)){
              $pp .= 'Ukuran: '.$ods->ukuran.', ';
            }
            if(!empty($ods->warna)){
              $pp .= 'Warna: '.$ods->warna.', ';
            }
            if(!empty($ods->rasa)){
              $pp .= 'Rasa: '.$ods->rasa.', ';
            }
            $pp = rtrim($pp,', ');
          ?>
          <tr>
            <td class="">
              <h4 style="margin-bottom: 0.25em;">
                <a href="<?php echo base_url_admin('ecommerce/produk/detail/'.$ods->c_produk_id); ?>" title="Lihat detail Product <?php echo $ods->nama; ?>" target="_blank"><?php echo $ods->nama; ?></a>
              </h4>
              <?php if(!empty($pp)){ ?>
              <p style="margin-bottom: 0;"><?php echo $pp; ?></p>
              <?php } ?>
            </td>
            <td class="text-center"><strong><?php echo number_format($ods->stok,0,',','.'); ?> Pcs</strong></td>
            <td class="text-center"><strong><?php echo number_format($ods->qty,0,',','.'); ?> Pcs</strong></td>
            <td class="text-right"><strong>$<?php echo number_format($ods->harga_asal,0,',','.'); ?></strong></td>
            <td class="text-right"><strong>$<?php echo number_format($ods->harga_asal - $ods->harga_jadi,0,',','.'); ?></strong></td>
            <td class="text-right"><strong>$<?php echo number_format($ods->qty*$ods->harga_jadi,0,',','.'); ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td colspan="5" class="text-right text-uppercase"><strong>Shipping:</strong></td>
            <td class="text-right"><strong>$<?php echo number_format($order->shipping_cost_total,0,',','.'); ?></strong></td>
          </tr>
          <tr style="display:none;">
            <td colspan="5" class="text-right text-uppercase"><strong>Discount + Unique Code:</strong></td>
            <td class="text-right text-danger"><strong>-$</strong></td>
          </tr>
          <tr class="active">
            <td colspan="5" class="text-right text-uppercase"><strong>Total:</strong></td>
            <td class="text-right"><strong>$<?php echo number_format($order->grand_total,0,',','.'); ?></strong></td>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- END Products Content -->
  </div>
  <!-- END Products Block -->

  <!-- Addresses -->
  <div class="row">
    <div class="col-sm-6">
      <!-- Billing Address Block -->
      <div class="block">
        <!-- Billing Address Title -->
        <div class="block-title">
          <h2><i class="fa fa-building-o"></i> <strong>Sender</strong> </h2>
        </div>
        <!-- END Billing Address Title -->

        <!-- Billing Address Content -->
        <h4><strong><?php echo $order->b_user_fnama; ?></strong></h4>
        <address>
          <i class="fa fa-phone"></i> <?php  ?><br>
          <i class="fa fa-envelope-o"></i> <a href="javascript:void(0)"><?=$this->site_email?></a>
        </address>
        <table class="table">
          <tr>
            <td>Buyer</td>
            <td>:</td>
            <td><a href="<?php echo base_url_admin('ecomerce/konsumen/detail/'.$order->id); ?>" target="_blank"><?php echo $order->pemesan_nama; ?></a></td>
          </tr>
          <tr>
            <td>Number</td>
            <td>:</td>
            <td><a href="https://api.whatsapp.com/send?phone=<?=$order->nation_code.$order->penerima_telp;?>" target="_blank"><?php echo $order->penerima_telp;?></a></td>
          </tr>
					<tr>
            <td>Voucher Code</td>
            <td>:</td>
            <td><strong><?php echo $order->kode; ?></strong></td>
          </tr>
					<tr>
            <td>Payment Code</td>
            <td>:</td>
            <td><?php echo $order->pembayaran; ?></td>
          </tr>
          <tr>
            <td>Payment Method</td>
            <td>:</td>
            <td><?php echo $pembayaran->deskripsi; ?></td>
          </tr>
          <tr>
            <td>Payment Status</td>
            <td>:</td>
            <td>
						<?php
							if($order->order_status == 'order_konfirmasi' || $order->order_status == 'order_konfirmasi_sudah' || $order->order_status == 'order_batal'){
								if(empty($order->confirm_bukti)){
									echo 'Not paid';
								}else{
									echo 'Payment Confirmed';
								}
							}else{
								echo 'Payment Confirmed';
							}
						?>
						</td>
          </tr>
        </table>
        <!-- END Billing Address Content -->
      </div>
      <!-- END Billing Address Block -->
    </div>
    <div class="col-sm-6">
      <!-- Shipping Address Block -->
      <div class="block">
        <!-- Shipping Address Title -->
        <div class="block-title">
          <h2><i class="fa fa-building-o"></i> <strong>Receiver</strong></h2>
        </div>
        <!-- END Shipping Address Title -->

        <!-- Shipping Address Content -->
        <h4><strong><?php echo $order->penerima_nama; ?> <small><a href="https://api.whatsapp.com/send?phone=<?=$order->nation_code.$order->penerima_telp; ?>" target="_blank"><i class="fa fa-whatsapp"></i> <?php echo $order->penerima_telp; ?></a></small></strong></h4>
        <address>
          <?php echo $order->penerima_alamat; ?><br>
          <?php echo $order->penerima_kecamatan.', '.$order->penerima_kabkota; ?><br>
          <?php echo $order->penerima_provinsi.', '.$order->penerima_negara.' '.$order->penerima_kodepos; ?><br><br>
        </address>
        <!-- END Shipping Address Content -->
        <h4>
          <strong><i class="fa fa-truck"></i> <?php echo $order->kurir; ?></strong>
        </h4>
				<p>Total Weight: <?=$order->berat_total;?> lb</p>
        <p style="padding:0;line-height:1; margin-bottom: 0.9em;">
          <?php if(empty($order->noresi)){ echo 'Unposted'; }else{ echo 'Delivered with Airwaybill number: <strong>'.$order->noresi.'</strong>'; } ?>
        </p>

				<!--rating -->
        <h4 style="margin-top: 1em;">
          <strong><i class="fa fa-star"></i> Rating</strong>
        </h4>
				<?php if($order->order_status == 'order_selesai'){ ?>
        <p style="padding:0;line-height:1; margin-bottom: 0.9em;">
          <?php
						for($rti=1;$rti<=5;$rti++){
							if($order->rating_nilai<=$rti){
								echo '<i class="fa fa-star-o"></i>';
							}else{
								echo '<i class="fa fa-star"></i>';
							}
						}
						echo '<br>';
						if(strlen($order->rating_komentar)>0){
							echo $order->rating_komentar;
						}else{
							echo '-';
						}
					?>
        </p>
				<?php }else{ ?>
				<p style="padding:0;line-height:1; margin-bottom: 0.9em;">Order Hasn't done</p>
				<?php } ?>
				<!-- end rating -->

      </div>
      <!-- END Shipping Address Block -->
    </div>
  </div>
  <!-- END Addresses -->

  <!-- konfirmasi_order -->
  <div class="row" style="<?php if(empty($order->confirm_bukti)) echo 'display:none;'; ?>">
    <div class="col-sm-6">
      <div class="block">
        <div class="block-title">
          <h2><i class="fa fa-university"></i> <strong>Payment Confirmation</strong> </h2>
        </div>
        <table class="table">
          <tr>
            <td>Confirmation Date</td>
            <td>:</td>
            <td><?php echo $this->__dateIndonesia($order->confirm_tgl); ?></td>
          </tr>
          <tr>
            <td>Sent from</td>
            <td>:</td>
            <td><?php echo $order->confirm_dari.' via '.$order->confirm_nama.' ('.$order->confirm_norek.')'; ?></td>
          </tr>
          <tr>
            <td>Shipping Method</td>
            <td>:</td>
            <td><?php echo ucfirst($order->confirm_cara); ?></td>
          </tr>
          <tr>
            <td>Sent to</td>
            <td>:</td>
            <td><?php echo ucfirst($order->confirm_ke); ?></td>
          </tr>
          <tr>
            <td>Transfer Total</td>
            <td>:</td>
            <td>$<?php echo number_format($order->confirm_nom,0,'.',','); ?></td>
          </tr>
          <tr>
            <td>Verified by</td>
            <td>:</td>
            <td><?php echo $order->confirmed_by; ?></td>
          </tr>
        </table>
      </div>
    </div>

    <!-- END konfirmasi_order Gambar-->
    <div class="col-sm-6">
      <div class="block">
        <div class="block-title">
          <h2><i class="fa fa-file-image-o"></i> <strong>Confirm Transfer Evidence</strong></h2>
        </div>
        <div class="thmb" style="background-image:url('<?php echo base_url($order->confirm_bukti); ?>'); ">
          <a href="<?php echo base_url($order->confirm_bukti); ?>" title="Klik untuk lihat lebih jelas" target="_blank">
            <div class="" style="height: 189px;">&nbsp;</div>
          </a>
        </div>
      </div>
    </div>
  </div>
  <!-- END konfirmasi_order-->


  <!-- purchase qc packing resi -->
  <div class="row">


    <?php if(!empty($order->foto_qc)){ ?>
    <!-- QC -->
    <div class="col-sm-6">
      <div class="block">
        <div class="block-title">
          <h2><i class="fa fa-file-o"></i> <strong>QC</strong></h2>
        </div>
        <div class="thmb" style="background-image:url('<?php echo base_url($order->foto_qc); ?>'); ">
          <a href="<?php echo base_url($order->foto_qc); ?>" title="Klik untuk lihat lebih jelas" target="_blank">
            <div class="" style="height: 189px;">&nbsp;</div>
          </a>
        </div>
        <table class="table">
          <tr>
            <td>QC Officer</td>
            <td>:</td>
            <td><?php echo $order->qc_name; ?></td>
          </tr>
        </table>
      </div>
    </div>
    <!-- QC -->
    <?php } ?>

    <?php if(!empty($order->foto_packing)){ ?>
    <!-- Packing -->
    <div class="col-sm-6">
      <div class="block">
        <div class="block-title">
          <h2><i class="fa fa-file-o"></i> <strong>Packing</strong> </h2>
        </div>
        <div class="thmb">
          <a href="<?php echo base_url($order->foto_packing); ?>" title="Klik untuk lihat lebih jelas" target="_blank">
            <img src="<?php echo base_url($order->foto_packing); ?>" class="img-responsive" />
          </a>
        </div>
        <table class="table">
          <tr>
            <td>Packing Officer</td>
            <td>:</td>
            <td><?php echo $order->packing_name; ?></td>
          </tr>
        </table>
      </div>
    </div>
    <!-- Purchasing -->
    <?php } ?>

    <?php if(!empty($order->noresi)){ ?>
    <!-- Resi Pengiriman-->
    <div class="col-sm-6">
      <div class="block">
        <div class="block-title">
          <h2><i class="fa fa-file-o"></i> <strong>Shipment</strong></h2>
        </div>
        <table class="table">
          <tr>
            <td>Airwaybill Number</td>
            <td>:</td>
            <td><?php echo $order->noresi; ?></td>
          </tr>
        </table>
      </div>
    </div>
    <!-- Resi Pengiriman -->
    <?php } ?>

  </div>
  <!-- END konfirmasi_order-->

  <!-- Log Block -->
  <div class="block">
    <!-- Log Title -->
    <div class="block-title">
      <h2><i class="fa fa-file-text-o"></i> <strong>Log</strong> Messages</h2>
    </div>
    <!-- END Log Title -->

    <!-- Log Content -->
    <div class="table-responsive">
			<?php if($order->order_status == 'order_batal' && $this->__validateDate($order->date_order_batal)){ ?>
			<div class="panel panel-default">
				<pre style="color: #efefef"><?=$order->catatan_admin?></pre>
			</div>
			<?php } ?>
      <table class="table table-bordered table-vcenter">
        <tbody>
          <?php if($order->order_status == 'order_batal' && $this->__validateDate($order->date_order_batal)){ ?>
          <tr>
            <td>
              <span class="label label-primary">Order</span>
            </td>
            <td class="text-center"><?php echo $this->__dateIndonesia($order->date_order_batal,'tanggal_jam'); ?></td>
            <td><a href="#"><?php echo $order->pemesan_nama; ?></a></td>
            <td class="text-danger"><i class="fa fa-fw fa-exclamation-triangle"></i> <strong>Ordercanceled <i class="fa fa-times"></i></strong></td>
          </tr>
          <?php } ?>


          <?php if($this->__validateDate($order->date_order_kirim)){ ?>
          <tr>
            <td>
              <span class="label label-primary">Order</span>
            </td>
            <td class="text-center"><?php echo $this->__dateIndonesia($order->date_order_kirim,'tanggal_jam'); ?></td>
            <td><a href="#"><?php echo $order->confirmed_by; ?></a></td>
            <td class="text-success"><strong>Sent by Airwaybill number <?php echo $order->noresi; ?></strong></td>
          </tr>
          <?php } ?>

          <?php if($this->__validateDate($order->date_order_packing)){ ?>
          <tr>
            <td>
              <span class="label label-primary">Order</span>
            </td>
            <td class="text-center"><?php echo $this->__dateIndonesia($order->date_order_packing,'tanggal_jam'); ?></td>
            <td><a href="#"><?php echo $order->confirmed_by; ?></a></td>
            <td class=""><strong>packed</strong></td>
          </tr>
          <?php } ?>

          <?php if($this->__validateDate($order->date_order_qc)){ ?>
          <tr>
            <td>
              <span class="label label-primary">Order</span>
            </td>
            <td class="text-center"><?php echo $this->__dateIndonesia($order->date_order_qc,'tanggal_jam'); ?></td>
            <td><a href="#"><?php echo $order->confirmed_by; ?></a></td>
            <td class=""><strong>QC has done</strong></td>
          </tr>
          <?php } ?>

          <?php if($this->__validateDate($order->date_store)){ ?>
          <tr>
            <td>
              <span class="label label-info">Order</span>
            </td>
            <td class="text-center"><?php echo $this->__dateIndonesia($order->date_store,'tanggal_jam'); ?></td>
            <td><a href="#">System</a></td>
            <td><strong>store sales</strong></td>
          </tr>
          <?php } ?>

          <?php if($this->__validateDate($order->date_order_pembelian)){ ?>
          <tr>
            <td>
              <span class="label label-info">Order</span>
            </td>
            <td class="text-center"><?php echo $this->__dateIndonesia($order->date_order_pembelian,'tanggal_jam'); ?></td>
            <td><a href="#">Sistem</a></td>
            <td><strong>Good is being purchased</strong></td>
          </tr>
          <?php } ?>

          <?php if($this->__validateDate($order->date_order_proses)){ ?>
          <tr>
            <td>
              <span class="label label-success">Payment</span>
            </td>
            <td class="text-center"><?php echo $this->__dateIndonesia($order->date_order_proses,'tanggal_jam'); ?></td>
            <td><a href="#"><?php echo $order->confirmed_by; ?></a></td>
            <td class="text-success"><strong>Payment Verified $<?php echo number_format($order->grand_total,0,'.',','); ?> <i class="fa fa-check"></i></strong></td>
          </tr>
          <?php } ?>


          <?php if($this->__validateDate($order->date_order_konfirmasi_sudah)){ ?>
          <tr>
            <td>
              <span class="label label-success">Pembayaran</span>
            </td>
            <td class="text-center"><?php echo $this->__dateIndonesia($order->date_order_konfirmasi_sudah,'tanggal_jam'); ?></td>
            <td><a href="<?php echo base_url_admin('ecommerce/member/detail/'.$order->b_user_id); ?>" target="_blank"><?php echo $order->pemesan_nama; ?></a></td>
            <td><strong>Menunggu Verifikasi oleh Admin</strong></td>
          </tr>
          <?php } ?>

          <?php if($this->__validateDate($order->date_order_konfirmasi)){ ?>
          <tr>
            <td>
              <span class="label label-success">Payment</span>
            </td>
            <td class="text-center"><?php echo $this->__dateIndonesia($order->date_order_konfirmasi,'tanggal_jam'); ?></td>
            <td><a href="javascript:void(0)">System</a></td>
            <td><strong>Wait for Confirmation</strong></td>
          </tr>
          <?php } ?>


          <?php if($this->__validateDate($order->date_order)){ ?>
          <tr>
            <td>
              <span class="label label-primary">Orders</span>
            </td>
            <td class="text-center"><?php echo $this->__dateIndonesia($order->date_order,'tanggal_jam'); ?></td>
            <td><a href="<?php echo base_url_admin('ecommerce/member/detail/'.$order->b_user_id); ?>" target="_blank"><?php echo $order->pemesan_nama; ?></a></td>
            <td><strong>Order</strong></td>
          </tr>
          <?php } ?>

        </tbody>
      </table>
    </div>
    <!-- END Log Content -->
  </div>
  <!-- END Log Block -->
</div>
