<!-- Main Content -->
    <div class="container m-t-3">
      <div class="row">

        <!-- Account Sidebar -->
				<?php $this->getThemeElement("account/sidebar",$__forward); ?>
        <!-- End Account Sidebar -->

        <!-- My Profile Content -->
        <div class="col-sm-8 col-md-9">
          <div class="title m-b-2"><span>Edit Alamat</span></div>
          <div class="row">
            <div class="col-xs-12">
              <form>
                <div class="form-group">
                  <label for="editAddress">Alamat</label>
                  <textarea class="form-control" rows="3" id="Editaddress"></textarea>
                </div>
                <div class="form-group">
                  <label for="inegara">Negara (*)</label>
                  <select id="inegara" class="form-control">
                    <option value="IDN">Indonesia</option>
                  <option value="MAS">Malaysia</option>
                </select>
                  <input type="hidden" id="inegara" name="negara" value="" required />
              </div>
                <div class="form-group">
                <label id="label_sprovinsi" for="sprovinsi">Provinsi / State</label>
                <select id="sprovinsi" class="form-control">
                  <option value=""> --- Please Select --- </option>
                  <option value="3513">Aberdeen</option>
                  <option value="3514">Aberdeenshire</option>
                  <option value="3515">Anglesey</option>
                  <option value="3516">Angus</option>
                </select>
                <label id="label_iprovinsi" for="iprovinsi">Provinsi / State</label>
                <input id="iprovinsi" type="text" name="provinsi" value="" required  class="form-control" />
              </div>
              <div class="form-group">
                <label id="label_ikabkota" for="ikabkota">Kota (*)</label>
                <input id="ikabkota" type="text" class="form-control" id="cityInput" placeholder="Kota / City" required />
                <label id="label_skabkota" for="skabkota">Kota (*)</label>
                <select id="skabkota" class="form-control">
                  <option value=""> --- Please Select --- </option>
                </select>
              </div>
              <div class="form-group">
                <label id="label_ikecamatan" for="ikecamatan">Kecamatan (*)</label>
                <input id="ikecamatan" type="text" class="form-control" id="cityInput" placeholder="District" required />
                <label id="label_skecamatan" for="skecamatan">Kecamatan (*)</label>
                <select id="skecamatan" class="form-control">
                  <option value=""> --- Please Select --- </option>
                </select>
              </div>
                <div class="form-group">
                  <label for="editPostcode">Post Code</label>
                  <input type="text" class="form-control" id="editPostcode" placeholder="Post Code">
                </div>
                <button type="submit" class="btn btn-default btn-theme"><i class="fa fa-check"></i> Save Changes</button>
              </form>
            </div>
          </div>
        </div>
        <!-- End My Profile Content -->

      </div>
    </div>
    <!-- End Main Content -->
