<?php $is_belum = 1; ?>
<div class="wijet-profile-main margintopall10">
  <div class="row">
    <div class="col-xs-8">
      <h2 class="section-title">Vendor</h2>
    </div>
    <div class="col-xs-4" style="padding-top: 1em; text-align:right;">
      <a href="<?php echo base_url('produk/'); ?>" class="aselengkapnya">Lainnya</a>
    </div>
  </div>
<?php if($is_belum){ ?>
  <div class="row">
    <div class="col-md-12">
      Belum ada data...
    </div>
  </div>
<?php } else { ?>
  <div class="row ">
    <div class="col-xs-4 ">
      <a href="<?php echo base_url('account/produk'); ?>" class="btn btn-block btn-transparent-2">
        <img src="<?php echo base_url('media/produk/thumb/'); ?>gorealem-kerupuk-gurilem-rasa-biasa.jpg" class="img-responsive" />
      </a>
    </div>

    <div class="col-xs-8 " style="padding: 0; padding-top: 0.75em;">
      <div class="row terlaris-list">
        <ol class="ol-terlaris">
          <li>
            <a href="<?php echo base_url('account/produk'); ?>" class="">
              Rolciz Green Tea - <small>Foodziah</small>
            </a>
          </li>
          <li>
            <a href="<?php echo base_url('account/produk'); ?>" class="">
              Gorealem Pedas - <small>Foodziah</small>
            </a>
          </li>
          <li>
            <a href="<?php echo base_url('account/produk'); ?>" class="">
              Gorealem Biasa - <small>Foodziah</small>
            </a>
          </li>
        </ol>

      </div>

    </div>

  </div>
<?php } ?>
</div>
