
<div class="wijet-profile-main">
  <h2 class="section-title">Kategori</h2>
  <div class="row">

    <?php if(isset($kategori)){ foreach($kategori as $k){ ?>
    <div class="col-xs-4 " style="min-height: 180px;">
      <a href="<?php echo base_url('kategori/produk/'.$k->slug); ?>" title="Lihat semua produk dengan kategori <?php echo $k->nama; ?>" class="btn btn-block btn-transparent-2">
        <img src="<?php echo $k->image; ?>" alt="<?php echo $k->nama; ?>" class="img-responsive" />
        <p style="  white-space: normal;"><?php echo $k->nama; ?></p>
      </a>
    </div>
    <?php }} ?>

  </div>
</div>
