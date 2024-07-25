<style>
.wijet-profile-main {
  padding: 1em;
  background-color: #ffffff;
  box-shadow: 0 1px 1px 0 rgba(0,0,0,.05);
  border: 1px #e5e5e5 solid;
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
        <div class="title m-b-2"><span>Alamat Baru</span></div>
        <div class="row">
          <div class="col-xs-12">

            <form id="f_address_add" method="post" action="<?php echo base_url('account/address/add'); ?>">
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="inama_alamat">Label Alamat *</label>
                    <input id="inama_alamat" name="nama_alamat" type="text" value="" class="form-control" required />
                  </div>
                  <div class="col-md-6">
                    &nbsp;
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="inama_penerima">Penerima *</label>
                    <input id="inama_penerima" name="nama_penerima" type="text" value="" class="form-control" required />
                  </div>
                  <div class="col-md-6">
                    <label for="itelp">Telepon Penerima *</label>
                    <input id="itelp" name="telp" type="text" class="form-control" required />
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="editAddress">Alamat *</label>
                <textarea id="ialamat"  class="form-control" name="alamat" rows="3" required></textarea>
              </div>
              <div class="form-group">
                <div class="row">

                  <div class="col-md-6">
                    <label for="snegara">Negara *</label>
                    <select id="snegara" name="negara" class="form-control">
                      <option value=""> --- Please Select --- </option>
                      <?php foreach($negara as $n){ ?>
                      <?php if($n->kode != 'ID') continue; ?>
                      <option value="<?php echo $n->nama; ?>" <?php if($n->kode == 'ID') echo 'selected="selected"'; ?>><?php echo $n->nama; ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label for="sprovinsi">Provinsi *</label>
                    <select id="sprovinsi" class="form-control" >
                      <option value=""> --- Please Select --- </option>
                      <option value="3513">Aberdeen</option>
                      <option value="3514">Aberdeenshire</option>
                      <option value="3515">Anglesey</option>
                      <option value="3516">Angus</option>
                    </select>
                    <input id="iprovinsi" type="text" name="provinsi" value="" class="form-control" required />
                  </div>

                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="ikabkota">Kabupaten / Kota (*)</label>
                    <input id="ikabkota" name="kabkota" type="text" class="form-control" placeholder="Kota / City" />
                    <select id="skabkota" class="form-control" >
                      <option value=""> --- Please Select --- </option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="ikecamatan">Kecamatan (*)</label>
                    <input id="ikecamatan" name="kecamatan" type="text" class="form-control" placeholder="District" />
                    <select id="skecamatan" class="form-control">
                      <option value=""> --- Please Select --- </option>
                    </select>
                    <input id="ikelurahan" name="kelurahan" type="hidden" value="-" />
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="kodepos">Kode Pos</label>
                    <input id="ikodepos" name="kodepos" type="text" class="form-control" />
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-default btn-theme"><i class="fa fa-check"></i> Simpan</button>
            </form>

            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End My Profile Content -->

  </div>
</div>
    <!-- End Main Content -->
