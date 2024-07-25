<div class="row">
  <div class="col-lg-6">
    <!-- Widget -->
    <a href="<?=base_url_admin('ecommerce/order/?utype=order_konfirmasi_sudah')?>" class="widget widget-hover-effect1">
      <div class="widget-simple">
        <div class="widget-icon pull-left themed-background-autumn animation-fadeIn">
          <i class="fa fa-check"></i>
        </div>
        <h3 class="widget-content text-right animation-pullDown">
          <strong><?php echo number_format($order_konfirmasi_sudah_count); ?></strong> Orderan<br>
          <small>Telah Konfirmasi</small>
        </h3>
      </div>
    </a>
    <!-- END Widget -->
  </div>
  <div class="col-lg-6">
    <!-- Widget -->
    <a href="<?=base_url_admin('ecommerce/order/?utype=order_konfirmasi_sudah,order_cekstok,order_qc,order_packing,order_kirim')?>" class="widget widget-hover-effect1">
      <div class="widget-simple">
        <div class="widget-icon pull-left themed-background-spring animation-fadeIn">
          <i class="fa fa-cog"></i>
        </div>
        <h3 class="widget-content text-right animation-pullDown">
          <strong><?php echo number_format($order_proses_count); ?></strong> Orderan<br>
          <small>Diproses</small>
        </h3>
      </div>
    </a>
    <!-- END Widget -->
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <!-- Your Plan Widget -->
    <div class="widget">
      <div class="widget-extra themed-background-dark">
        <h3 class="widget-content-light">
          Info <strong>Orderan</strong>
          <small><a href="<?=base_url_admin('ecommerce/order/?utype=')?>order_konfirmasi,order_konfirmasi_sudah,order_cekstok,order_qc,order_packing,order_kirim,order_selesai"><strong>selengkapnya</strong></a></small>
        </h3>
      </div>
      <div class="widget-extra-full">
        <div class="row text-center">
          <div class="col-xs-4 col-lg-3">
            <h3>
              <a href="<?=base_url_admin('ecommerce/order/?')?>utype=order_konfirmasi">
                <strong><?=$order_counts->order_konfirmasi?></strong> <small>/<?=$order_counts->total_konfirmasi?></small><br>
                <small><i class="fa fa-folder-open-o"></i> Belum Konfirmasi</small>
              </a>
            </h3>
          </div>
          <div class="col-xs-4 col-lg-3">
            <h3>
              <a href="<?=base_url_admin('ecommerce/order/?')?>utype=order_konfirmasi_sudah">
                <strong><?=$order_counts->order_konfirmasi_sudah?></strong> <small>/<?=$order_counts->total_konfirmasi?></small><br>
                <small><i class="fa fa-hdd-o"></i> Sudah Konfirmasi</small>
              </a>
            </h3>
          </div>
          <div class="col-xs-4 col-lg-3">
            <h3>
              <a href="<?=base_url_admin('ecommerce/order/?')?>utype=order_cekstok,order_qc">
                <strong><?=($order_counts->order_qc+$order_counts->order_cekstok)?></strong> <small>/<?=$order_counts->total_proses?></small><br>
                <small><i class="fa fa-building-o"></i> Belum Diproses</small>
              </a>
            </h3>
          </div>
          <div class="col-xs-4 col-lg-3">
            <h3>
              <a href="<?=base_url_admin('ecommerce/order/?')?>utype=order_packing,order_kirim">
                <strong><?=$order_counts->order_packing?></strong> <small>/<?=$order_counts->total_selesai?></small><br>
                <small><i class="fa fa-envelope-o"></i> Belum Dikirim</small>
              </a>
            </h3>
          </div>
        </div>
      </div>
    </div>
    <!-- END Your Plan Widget -->
  </div>
</div>
