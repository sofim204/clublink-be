
<div class="wijet-profile-main ">
  <div class="row">
    <div class="col-xs-8">
      <h2 class="section-title">Terlaris</h2>
    </div>
    <div class="col-xs-4" style="padding-top: 1em; text-align:right;">
      <a href="<?php echo base_url('produk/'); ?>" class="aselengkapnya">Lainnya</a>
    </div>
  </div>
  <div class="row ">
    <div class="col-xs-4 ">
      <?php if(isset($produk_terjual[2])) $pt = $produk_terjual[2]; ?>
      <a href="<?php echo base_url('produk/'.$pt->slug); ?>" title="<?php echo $pt->nama; ?>" class="btn btn-block btn-transparent-2">
        <img src="<?php echo $pt->thumb; ?>" alt="<?php echo $pt->nama; ?>" class="img-responsive" />
      </a>
    </div>

    <div class="col-xs-8 " style="padding: 0;">
      <div class="row terlaris-list">
        <ol class="ol-terlaris">

          <?php if(isset($produk_terjual)){ foreach($produk_terjual as $pt){ ?>
          <li>
            <a href="<?php echo base_url('produk/'.$pt->slug); ?>" title="<?php echo $pt->nama; ?>" class="">
              <?php echo $pt->nama; ?>
            </a>
          </li>
          <?php }} ?>
        </ol>

      </div>

    </div>

  </div>
</div>
