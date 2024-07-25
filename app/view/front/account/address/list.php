<style>
</style>
<!-- Main Content -->
    <div class="container m-t-3">
      <div class="row">

        <!-- Account Sidebar -->
				<?php $this->getThemeElement("account/sidebar",$__forward); ?>
        <!-- End Account Sidebar -->

        <!-- My Profile Content -->
        <div class="col-sm-8 col-md-9" style="padding-right: 1.5em;">
          <div id="alamat_list" class="wijet-profile-main">
            <div class="title m-b-2">
              <span>Alamat Pilihan</span>
              <a href="<?php echo base_url(); ?>account/address/add/" title="Tambahkan alamat baru" class="btn btn-theme btn-sm pull-right"><i class="fa fa-plus"></i> Alamat Baru</a>
            </div>
            <?php if(isset($alamat[0])){ ?>
              <div class="panel panel-default" style="background-color: #efefef;">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-3 hidden-xs text-center">
                      <div style="padding: 1em 0.75em;background-color: #fff; border: 1px #ededed solid;">
                        <p><strong><?php echo $user_alamat->nama_penerima; ?></strong></p>
                        <p><?php echo $user_alamat->nama_alamat; ?></p>
                      </div>
                    </div>
                    <div class="col-md-7 col-xs-10">
                      <p><strong><?php echo $user_alamat->nama_penerima; ?></strong></p>
                      <p><?php echo $user_alamat->alamat.'<br /> Kec. '.$user_alamat->kecamatan.' '.$user_alamat->kabkota.' '.$user_alamat->kodepos.' <br />'.$user_alamat->provinsi.', '.$user_alamat->negara; ?></p>
                      <p>Telp <?php echo $user_alamat->telp; ?></p>
                    </div>

                    <div class="col-md-2 col-xs-2">
                      <div class="dropdown pull-right">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                          <i class="fa fa-cog"></i>
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                          <li><a href="<?php echo base_url(); ?>account/address/edit/<?php echo $user_alamat->id; ?>" class=""><i class="fa fa-pencil"></i> Ubah</a></li>
                          <li><a id="adelete" href="<?php echo base_url(); ?>account/address/delete/<?php echo $user_alamat->id; ?>" class="" data-id="<?php echo $user_alamat->id; ?>"><i class="fa fa-times"></i> Hapus</a></li>
                        </ul>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
              <?php $i=0; foreach($alamat as $al){ $i++; if($i==1) continue; ?>
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-3 hidden-xs text-center">
                      <div style="padding: 1em 0.75em; background-color: #fff; border: 1px #ededed solid;">
                        <p><strong><?php echo $al->nama_penerima; ?></strong></p>
                        <p><?php echo $al->nama_alamat; ?></p>
                      </div>
                    </div>
                    <div class="col-md-7 col-xs-10">
                      <p><strong><?php echo $al->nama_penerima; ?></strong></p>
                      <p><?php echo $al->alamat.'<br /> Kec. '.$al->kecamatan.' '.$al->kabkota.' '.$al->kodepos.' <br />'.$al->provinsi.', '.$al->negara; ?></p>
                      <p>Telp <?php echo $al->telp; ?></p>
                    </div>

                    <div class="col-md-2 col-xs-2">
                      <div class="dropdown pull-right">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                          <i class="fa fa-cog"></i>
                          <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                          <li><a href="<?php echo base_url(); ?>account/address/edit/<?php echo $al->id; ?>" class=""><i class="fa fa-pencil"></i> Ubah</a></li>
                          <li><a id="adefault" href="<?php echo base_url(); ?>account/address/default/<?php echo $al->id; ?>" class="" data-id="<?php echo $al->id; ?>"><i class="fa fa-star"></i> Set Utama</a></li>
                          <li><a id="adelete" href="<?php echo base_url(); ?>account/address/delete/<?php echo $al->id; ?>" class="" data-id="<?php echo $al->id; ?>"><i class="fa fa-times"></i> Hapus</a></li>
                        </ul>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
              <?php } ?>
              <?php } else { ?>
                <p>
                  Belum ada alamat yang tersimpan..
                </p>
              <?php } ?>
            <a href="<?php echo base_url(); ?>account/address/add/" title="Tambahkan alamat baru" class="btn btn-theme"><i class="fa fa-plus"></i>  Alamat Baru</a>
          </div>
        </div>
        <!-- End My Profile Content -->

      </div>
    </div>
    <!-- End Main Content -->
