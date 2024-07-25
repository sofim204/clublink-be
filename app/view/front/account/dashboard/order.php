<style>
.card {
    position: relative;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -ms-flex-direction: column;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: .25rem;
}
.card-body {
    -webkit-box-flex: 1;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    padding: 1.25rem;
}
.card-title {
    margin-bottom: .75rem;
    font-weight: bolder;
}
.card-img-top {
    width: 100%;
    border-top-left-radius: calc(.25rem - 1px);
    border-top-right-radius: calc(.25rem - 1px);
}
</style>
<?php $is_belum = 1; if(count($order)) $is_belum = 0; ?>
<div class="">
  <div class="row">
    <div class="col-xs-8">
      <h2 class="section-title">Belum Dibayar <?php if(!$is_belum) echo '('.$order_count.')'; ?></h2>
    </div>
    <div class="col-xs-4" style="padding-top: 1em; text-align:right;">
      <a href="<?php echo base_url('account/orderan/'); ?>" class="aselengkapnya">Lihat Orderan</a>
    </div>
  </div>
<?php if($is_belum){ ?>
  <div class="row">
    <div class="col-md-12">
      Tidak ada pesanan yang belum dibayar, <a href="<?php echo base_url('produk'); ?>">mau beli lagi?</a>
    </div>
  </div>
<?php } else { ?>
  <div class="row ">
    <?php foreach($order as $orderObject){ ?>
      <div class="col-md-3">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">TranID #<?=$orderObject->id?></h4>
            <p class="card-text">
              Pesanan <?=$orderObject->penerima_nama?> senilai  Rp<?=number_format($orderObject->grand_total,0,',','.')?>
              <strong>Belum Dibayar</strong>
            </p>
            <a href="<?=base_url('account/orderan/detail/'.$orderObject->id)?>" class="btn btn-info">Lihat</a>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
<?php } ?>
</div>
