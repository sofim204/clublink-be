<!-- Main Content -->
    <div class="container m-t-3">
      <div class="row">

        <!-- Account Sidebar -->
				<?php $this->getThemeElement("account/sidebar",$__forward); ?>
        <!-- End Account Sidebar -->

        <!-- My Profile Content -->
        <div class="col-sm-8 col-md-9">
          <div class="title m-b-2"><span>
            <i class="fa fa-shopping-cart" aria-hidden="true" style="margin-right: 5px;"></i>INFORMASI TOKO</span></div>
          <div class="row">
            <div class="col-xs-12">
                <a class="btn btn-primary btn-md" href="<?php echo base_url(); ?>account/tambah_toko">
                  <span><i class="fa fa-plus" style="margin-right: 5px;"></i></span>Tambah Toko</a>
              <br>
              <br>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <td>No</td>
                      <td>Logo</td>
                      <td>Nama Toko</td>
                      <td>Telepon</td>
                      <td>Website</td>
                      <td>Opsi</td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>12</td>
                      <td><img src="#" /></td>
                      <td>jangiman</td>
                      <td>0987654321</td>
                      <td>www.jangiman.com</td>
                      <td><button class="btn btn-success btn-sm" title="Edit Toko" onclick="#"><i class="fa fa-edit"></i></button>&nbsp;&nbsp;
                          <button class="btn btn-danger btn-sm" title="Hapus Toko" onclick="#"><i class="fa fa-trash"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td>13</td>
                      <td><img src="#" /></td>
                      <td>jangsoleh</td>
                      <td>0987654321</td>
                      <td>www.jangsoleh.com</td>
                      <td><button class="btn btn-success btn-sm" title="Edit Toko" onclick="#"><i class="fa fa-edit"></i></button>&nbsp;&nbsp;
                          <button class="btn btn-danger btn-sm" title="Hapus Toko" onclick="#"><i class="fa fa-trash"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- End My Profile Content -->

      </div>
    </div>
    <!-- End Main Content -->
