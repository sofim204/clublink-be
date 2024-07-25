<style>
thead th {
  text-align: center;
}
</style>
<!-- Main Content -->
    <div class="container m-t-3">
      <div class="row">

        <!-- Account Sidebar -->
				<?php $this->getThemeElement("account/sidebar",$__forward); ?>
        <!-- End Account Sidebar -->

        <!-- My Profile Content -->
        <div class="col-sm-8 col-md-9" style="padding-right: 1.5em;">
          <div class="wijet-profile-main">

              <div class="title m-b-2"><span>
                <i class="fa fa-shopping-cart" aria-hidden="true" style="margin-right: 5px;"></i>INFORMASI TOKO</span>
                <a href="<?php echo base_url(); ?>account/toko/add/" title="Tambahkan toko baru" class="btn btn-theme btn-sm pull-right"><i class="fa fa-plus"></i> Toko Baru</a>
              </div>


              <?php if(count($store)>0){ $i=0;  ?>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Logo</th>
                      <th>Nama Toko</th>
                      <th>Telepon</th>
                      <th>Website</th>
                      <th>Opsi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach($store as $al){ ?>
                    <tr>
                      <td ><?php echo $al->id; ?></td>
                      <td class="text-center"><img src="<?php echo base_url($user_store->logo); ?>" alt="Logo <?php echo $al->nama; ?>" /></td>
                      <td><?php echo $al->nama; ?></td>
                      <td><?php echo $al->telp; ?></td>
                      <td><?php echo $al->website; ?></td>
                      <td>
                        <a href="<?php echo base_url(); ?>account/toko/edit/<?php echo $al->id; ?>" class="btn btn-success btn-sm" title="Edit Toko" onclick="#"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
                        <a id="adelete" href="<?php echo base_url(); ?>account/toko/delete/<?php echo $al->id; ?>" class="btn btn-danger btn-sm" title="Hapus Toko" onclick="#"><i class="fa fa-trash"></i></a>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>


              <?php } else { ?>
                <p>
                  Belum ada toko yang tersimpan..
                </p>
              <?php } ?>
            <a href="<?php echo base_url(); ?>account/toko/add/" title="Tambahkan toko baru" class="btn btn-theme"><i class="fa fa-plus"></i> Toko Baru</a>

            </div>
          </div>



        </div>
        <!-- End My Profile Content -->

                </div>
      </div>
    </div>
    <!-- End Main Content -->
