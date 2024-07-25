
<div class="col-md-12">
  <h2 class="section-title">Produk Baru</h2>
  <div class="row">

    <?php if(isset($produk_baru)){ foreach($produk_baru as $pb){ ?>
    <div class="col-xs-4 ">
      <a href="<?php echo base_url('produk/'.$pb->slug); ?>" title="lihat detail <?php echo $pb->nama; ?>" class="btn btn-block btn-transparent-2">
        <img src="<?php echo $pb->thumb; ?>" alt="<?php echo $pb->nama; ?>" class="img-responsive" />
        <p class="produk-nama" style="  white-space: normal;"><?php echo $pb->nama; ?></p>
      </a>
    </div>
    <?php } }?>


  </div>
</div>
