<?php
//$is_very_super_admin=1;
//$is_very_super_admin=1;
if (!isset($is_very_super_admin)) {
  $is_very_super_admin=0;
}
if (!isset($is_advanced)) {
  $is_advanced=0;
}
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
.product-wrapper {
  border: 1px #ededed solid;
  margin-bottom: 1em;
}
.product-wrapper-img {
  min-height: 135px;
  background-position: center center;
  background-size: cover;
  background-repeat: no-repeat;
  background-image: url('<?=base_url("media/produk/default.png")?>');
}
.product-wrapper-text {
  margin-bottom: 0.25em;
  border-top: 1px #ededed solid;
}
.product-wrapper-text h5{
  margin-bottom: 0.5em;
  font-weight: bolder;
  font-size: 0.95em;
}
.product-wrapper-text p{
  margin: 0;
  font-size: 0.9em;
}
.product-wrapper.rejected {
  opacity: 0.4;
}
.product-wrapper.rejected:hover {
  opacity: 1;
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
					<a href="<?=base_url_admin('ecommerce/transaction/buyer/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
  <ul class="breadcrumb breadcrumb-top">
    <li>Ecommerce</li>
    <li><a href="<?=base_url_admin('ecommerce/transaction/buyer/')?>">Transaction By Buyer</a></li>
    <li>Detail</li>
    <li>ORDER ID: #<?=$order->id?></li>
  </ul>

  <!-- order info -->
  <div class="block">
    <div class="block-title">
      <h2><i class="fa fa-file-text-o"></i> <strong>Order Information</strong></h2>
    </div>

    <div class="row">
      <!--left-->
      <div class="col-md-6">
        <table class="table table-striped  table-borderless">
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
            <th>Bank / Card</th>
            <td>:</td>
            <td><?=$this->__card2Text($order->code_bank)?> <?php if (isset($probj->issuerCountry)) {
              if (!empty($probj->issuerCountry)) {
                echo '('.strtoupper($probj->issuerCountry).')';
              }
            } ?></td>
          </tr>
        </table>
      </div>
      <!--end left-->

      <!--right-->
      <div class="col-md-6">
        <table class="table  table-striped  table-borderless">
          <tr>
            <th>Product(s) Price Total </th>
            <td>:</td>
            <td><?=$this->__m($order->sub_total)?></td>
          </tr>
          <tr>
            <th>Total Shipping Cost</th>
            <td>:</td>
            <td><?=$this->__m($order->ongkir_total)?></td>
          </tr>
          <tr>
            <th>Grand Total</th>
            <td>:</td>
            <td><?=$this->__m($order->grand_total)?></td>
          </tr>
          <tr>
            <th class="text-info">Selling Fee</th>
            <th class="text-info">:</th>
            <td class="text-info"><?=$this->__m($order->selling_fee)?></td>
          </tr>
          <tr>
            <th>MDR (<?=$order->pg_fee_percent?>%)</th>
            <th class="col-md-1">:</th>
            <td class="col-md-6"><?=$this->__m($order->pg_fee)?></td>
          </tr>
          <tr>
            <th>VAT</th>
            <th class="">:</th>
            <td class=""><?=$this->__m($order->pg_fee_vat)?></td>
          </tr>
          <tr>
            <th class="text-primary">PG Fee</th>
            <th class="text-primary">:</th>
            <td class="text-primary"><?=$this->__m($order->pg_fee+$order->pg_fee_vat)?></td>
          </tr>
          <?php if ($order->profit_amount>0) { ?>
            <tr>
              <th class="text-success">Total Profit</th>
              <th class="text-success">:</th>
              <td class="text-success"><?=$this->__m($order->profit_amount)?></td>
            </tr>
          <?php } else { ?>
            <tr>
              <th class="text-danger">Total Profit</th>
              <th class="text-danger">:</th>
              <td class="text-danger"><?=$this->__m($order->profit_amount)?></td>
            </tr>
          <?php } ?>
          <tr>
            <th class="text-warning">Refund Total</th>
            <th class="text-warning">:</th>
            <td class="text-warning"><?=$this->__m($order->refund_amount)?></td>
          </tr>
        </table>
      </div>
      <!--end right-->

    </div>
  </div>
  <!-- end order info -->

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



  <!-- Ordered items by seller -->
  <?php foreach ($order->detail as $seller) { ?>
    <div class="block">
      <div class="block-title">
        <h3><?=$seller->fnama?></h3>
      </div>
      <div class="row">
        <div class="col-md-2 text-center">
          <div class="panel panel-default">
            <div class="panel-body">
              <img src="<?=base_url($seller->image)?>" class="img-responsive" onerror="this.onerror=null;this.src='<?=base_url("media/pengguna/default.png")?>';" />
              <br />
              <a href="<?=base_url_admin("ecommerce/transaction/seller_detail/$order->id/$seller->id/")?>" class="btn btn-block btn-default btn-alt"><i class="fa fa-info-circle"></i> Detail</a>
            </div>
          </div>

        </div>
        <div class="col-md-10">
          <div class="row">
            <div class="col-md-6">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <tbody>
                    <tr>
                      <th>Total Item</th>
                      <td><?=$seller->total_item?></td>
                    </tr>
                    <tr>
                      <th>Total Qty</th>
                      <td><?=$seller->total_qty?></td>
                    </tr>
                    <tr>
                      <th>Shipment Service</th>
                      <td><?=ucwords($seller->shipment_service)." - ".$seller->shipment_type?></td>
                    </tr>
                    <tr>
                      <th>Product(s) Price Total</th>
                      <td><?=$this->__m($seller->sub_total)?></td>
                    </tr>
                    <tr>
                      <th>Shipment Cost</th>
                      <td><?=$this->__m($seller->shipment_cost+$seller->shipment_cost_add)?></td>
                    </tr>
                    <tr>
                      <th>Total</th>
                      <td><?=$this->__m($seller->grand_total)?></td>
                    </tr>
                    <tr>
                      <th>Selling Fee</th>
                      <td><?=$this->__m($seller->selling_fee)?></td>
                    </tr>
                    <tr>
                      <th>Profit</th>
                      <td><?=$this->__m($seller->profit_amount)?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-6">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <tbody>
                    <tr>
                      <th class="">Payment Status</th>
                      <td><?=$this->__paymentStatusText($order->payment_status)?></td>
                    </tr>
                    <tr>
                      <th class="">Shipment Service</th>
                      <td><?=ucwords($seller->shipment_service.' - '.$seller->shipment_type)?></td>
                    </tr>
                    <tr>
                      <th class="col-md-5">Seller Action</th>
                      <td><?=$this->__sellerStatusText($seller->seller_status)?></td>
                    </tr>
                    <tr>
                      <th class="">Shipping Status</th>
                      <td>
                        <?php
                        
                        //By Donny Dennison - 08-07-2020 16:16
                        //Request by Mr Jackie, add new shipment status "courier fail"
                        if ($seller->shipment_status == 'courier fail') {
                          echo ucfirst('courier Fail');       

                        } elseif ($seller->shipment_status == 'delivered' && strlen($seller->delivery_date) > 9 && strlen($seller->received_date) > 9) {
                          echo ucfirst('delivered');
                        } elseif ($seller->shipment_status == 'delivered' && strlen($seller->delivery_date) >9 && strlen($seller->received_date) <= 9) {
                          echo ucfirst('delivery in progress');
                        } elseif (($seller->shipment_status == 'process' || $seller->shipment_status == 'delivered') && strlen($seller->delivery_date) <= 9) {
                          echo ucfirst('not yet sent');
                        } elseif ($seller->shipment_status == 'succeed') {
                          echo ucfirst('received');
                        } else {
                          echo ucfirst($seller->shipment_status);
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th class="">Buyer Action</th>
                      <td><?=$this->__buyerConfirmedText($seller->buyer_confirmed)?></td>
                    </tr>
                    <tr>
                      <th class="">Settlement</th>
                      <td><?=$this->__settlementStatusText($seller->settlement_status)?></td>
                    </tr>
                    <tr>
                      <th>Refund</th>
                      <td><?=$this->__m($seller->refund_amount)?></td>
                    </tr>
                    <tr>
                      <th>Earning Total</th>
                      <td><?=$this->__m($seller->earning_total)?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Products Content -->
      <div class="panel panel-default">
        <div class="panel-heading">
          <b>Ordered Product(s)</b>
          <div class="btn-group pull-right">
            <a href="<?=base_url_admin("ecommerce/transactionhistory/detail/".$seller->d_order_id."/".$seller->id)?>" class="btn btn-default btn-xs">Transaction History</a>
            <a href="<?=base_url_admin("ecommerce/transaction/seller_detail/".$seller->d_order_id."/".$seller->id)?>" class="btn btn-default btn-xs">Transaction By Seller</a>
          </div>
          <span class="clearfix"></span>
        </div>
        <div class="panel-body">
          <div class="">
            <div class="row">
              <?php $ip = 0; foreach ($seller->items as $item) {
                if ($item->buyer_status == "rejected" && $item->settlement_status == "paid_to_buyer") {
                  continue;
                }
                $ip++; ?>
                <div class="col-md-2">
                  <div class="product-wrapper <?php if ($item->buyer_status == "rejected") {
                    echo 'rejected';
                  } ?>">
                  <div class="product-wrapper-img" style="background-image: url('<?=base_url($item->foto)?>')">
                    <img src="<?=base_url($item->foto)?>" class="img-responsive" onerror="this.onerror=null;this.src='<?=base_url("media/pengguna/default.png")?>';" />
                  </div>
                  <div class="product-wrapper-text text-center">
                    <h5><?=$this->__st($item->nama, 15)?></h5>
                    <p><?=$negara->simbol_mata_uang?><?=$item->harga_jual?></p>
                    <p><?=$item->qty?> <?=$item->satuan?></p>
                  </div>
                </div>
              </div>
              <?php
            } ?>
            <?php if (empty($ip)) { ?>
              <div class="col-md-12">
                <p class="text-info">All item(s) has been rejected by buyer</p>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <!-- END Products Content -->
    <div class="row">
      <div class="col-md-12">
        <br />
      </div>
    </div>
  </div>
<?php } ?>
<!-- END Ordered items by seller -->
</div>
