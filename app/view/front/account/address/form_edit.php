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
        <div class="title m-b-2"><span>Edit Alamat</span></div>
        <div class="row">
          <div class="col-xs-12">

            <form id="f_address_edit" method="post" action="<?php echo base_url('account/address/edit/'.$alamat->id); ?>">
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="inama_alamat">Label Alamat *</label>
                    <input id="inama_alamat" name="nama_alamat" type="text" value="<?php echo $alamat->nama_alamat; ?>" class="form-control" required />
                  </div>
                  <div class="col-md-6">
                    &nbsp;
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="inama_penerima">Penerima *</label>
                    <input id="inama_penerima" name="nama_penerima" type="text" value="<?php echo $alamat->nama_penerima; ?>" class="form-control" required />
                  </div>
                  <div class="col-md-6">
                    <label for="itelp">Telepon Penerima *</label>
                    <input id="itelp" name="telp" type="text" class="form-control" value="<?php echo $alamat->telp; ?>" required />
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="editAddress">Alamat *</label>
                <textarea id="ialamat"  class="form-control" name="alamat" rows="3" required><?php echo $alamat->alamat; ?></textarea>
              </div>
              <div class="form-group">
                <div class="row">

                  <div class="col-md-6">
                    <label for="snegara">Negara *</label>
                    <select id="snegara" name="negara" class="form-control">
                      <option value=""> --- Please Select --- </option>
                      <?php foreach($negara as $n){ ?>
                      <?php if($n->kode != 'ID') continue; ?>
                      <option value="<?php echo $n->nama; ?>" <?php if($n->nama == $alamat->negara) echo 'selected="selected"'; ?>><?php echo $n->nama; ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label for="sprovinsi">Provinsi *</label>
                    <select id="sprovinsi" class="form-control" >
                      <option value="<?php echo $alamat->provinsi; ?>"><?php echo $alamat->provinsi; ?></option>
                    </select>
                    <input id="iprovinsi" type="text" name="provinsi" value="<?php echo $alamat->provinsi; ?>" class="form-control" required />
                  </div>

                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="ikabkota">Kabupaten / Kota (*)</label>
                    <input id="ikabkota" name="kabkota" type="text" class="form-control" placeholder="Kota / City" value="<?php echo $alamat->kabkota; ?>" />
                    <select id="skabkota" class="form-control" >
                      <option value="<?php echo $alamat->kabkota; ?>"><?php echo $alamat->kabkota; ?></option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="ikecamatan">Kecamatan (*)</label>
                    <input id="ikecamatan" name="kecamatan" type="text" class="form-control" placeholder="District" value="<?php echo $alamat->kecamatan; ?>" />
                    <select id="skecamatan" class="form-control">
                      <option value="<?php echo $alamat->kecamatan; ?>"><?php echo $alamat->kecamatan; ?></option>
                    </select>
                    <input id="ikelurahan" name="kelurahan" type="hidden" value="<?php echo $alamat->kelurahan; ?>" />
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <label for="kodepos">Kode Pos *</label>
                    <input id="ikodepos" name="kodepos" type="text" class="form-control" value="<?php echo $alamat->kodepos; ?>" required />
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
