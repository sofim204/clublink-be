<!-- Main Content -->
    <div class="container m-t-3">
      <div class="row">

        <!-- Checkout Form -->
        <div class="col-md-12">
          <div class="title"><span>Myprofile</span></div>
          <form>
            <div class="row">
              <div class="form-group col-sm-6">
                <label for="firstNameInput">First Name (*)</label>
                <input type="text" class="form-control" id="firstNameInput" placeholder="First Name">
              </div>
              <div class="form-group col-sm-6">
                <label for="lastNameInput">Last Name</label>
                <input type="text" class="form-control" id="lastNameInput" placeholder="Last Name">
              </div>
              <div class="form-group col-sm-6">
                <label for="emailInput">Email Address (*)</label>
                <input type="email" class="form-control" id="emailInput" placeholder="Email Address">
              </div>
              <div class="form-group col-sm-6">
                <label for="phoneInput">Phone Number (*)</label>
                <input type="text" class="form-control" id="phoneInput" placeholder="Phone Number">
              </div>
              <div class="form-group col-sm-12">
                <label for="addressInput">Address (*)</label>
                <textarea class="form-control" rows="3" id="addressInput"></textarea>
              </div>
              <div class="form-group col-sm-6">
                <label for="postInput">Post Code (*)</label>
                <input type="text" class="form-control" id="postInput" placeholder="Post Code">
              </div>
              <div class="form-group col-sm-6">
                <label for="inegara">Negara (*)</label>
                <select id="inegara" class="form-control" >
                  <option value="IDN">Indonesia</option>                  <option value="MAS">Malaysia</option>                </select>
                <input type="hidden" id="inegara" name="negara" value="" required />              </div>              <div class="form-group col-sm-6">                <label id="label_sprovinsi" for="sprovinsi">Provinsi / State</label>                <select id="sprovinsi" class="form-control">                  <option value=""> --- Please Select --- </option>                  <option value="3513">Aberdeen</option>                  <option value="3514">Aberdeenshire</option>                  <option value="3515">Anglesey</option>                  <option value="3516">Angus</option>                </select>                <label id="label_iprovinsi" for="iprovinsi">Provinsi / State</label>                <input id="iprovinsi" type="text" name="provinsi" value="" required  class="form-control" />              </div>
              <div class="form-group col-sm-6">
                <label id="label_ikabkota" for="ikabkota">Kota (*)</label>
                <input id="ikabkota" type="text" class="form-control" id="cityInput" placeholder="Kota / City" required />
                <label id="label_skabkota" for="skabkota">Kota (*)</label>
                <select id="skabkota" class="form-control">
                  <option value=""> --- Please Select --- </option>
                </select>
              </div>
              <div class="form-group col-sm-6">
                <label id="label_ikecamatan" for="ikecamatan">Kecamatan (*)</label>
                <input id="ikecamatan" type="text" class="form-control" id="cityInput" placeholder="District" required />
                <label id="label_skecamatan" for="skecamatan">Kecamatan (*)</label>
                <select id="skecamatan" class="form-control">
                  <option value=""> --- Please Select --- </option>
                </select>
              </div>            </div>              <button type="submit" class="btn btn-default btn-theme"><i class="fa fa-check"></i> Save </button>            </form>          </div>        <!-- End Checkout Form -->      </div>    </div>    <!-- End Main Content -->