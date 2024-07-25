<!-- Main Content -->
    <div class="container m-t-3">
      <div class="row">

        <!-- Account Sidebar -->
				<?php $this->getThemeElement("account/sidebar",$__forward); ?>
        <!-- End Account Sidebar -->

        <!-- My Profile Content -->
        <div class="col-sm-8 col-md-9">
          <div class="title m-b-2"><span>
            <i class="fa fa-shopping-cart" aria-hidden="true" style="margin-right: 5px;"></i>INFORMASI TOKO</span>
          </div>
          <div class="row">
            <div class="col-xs-12 col-sm-4 text-center">
                <p class="text-center">Logo Toko (klik gambar untuk upload) <br />(tidak wajib)</p>
                <form id="form_upload" onSubmit="return false" method="post" enctype="multipart/form-data">

                </form>
                <img style="cursor: pointer;" id="modal_photo"  onclick="performClick('modal_photo_upload')" src="https://www.bandros.co.id/file/front/images/no_image.png" alt="photo-comment" width="200" />
                <div id="progressbox" style="display:none;"><div id="progressbar"></div><div id="statustxt">0%</div></div>
                <div id="output"></div>
            </div>
            <div class="col-xs-12 col-sm-8">
                <form id="form_toko" method="post" enctype="multipart/form-data">
                    <input  type="file" name="foto" id="modal_photo_upload" class="hidden">

                    <div class="form-group">
                        <label for="nama_toko">Nama Toko </label>
                        <input type="hidden">
                        <input name="nama_foto" type="hidden">
                        <input placeholder="Nama Toko" name="nama_toko" class="form-control" required type="text" required >
                    </div>
                    <div class="form-group">
                        <label for="telp">Telp. Toko </label>
                        <input placeholder="No. Telp. Toko" name="telp" class="form-control" required type="text" onkeypress="return isNumberKey(event)" required >
                    </div>
                    <div class="form-group">
                        <label for="website">Website (tidak wajib)</label>
                        <input placeholder="Alamat Website Toko" name="website" class="form-control" type="text" >
                    </div>

                <br/>
                <div>
                    <button class="btn btn-primary btn-lg" type="submit">Simpan Toko</button>
                    <br/>
                </div><!-- /.place-order-button -->
                </form>
                <br/>
            </div>


          </div>
        </div>
        <!-- End My Profile Content -->

      </div>
    </div>
    <!-- End Main Content -->
    <script type="text/javascript">
    function performClick(elemId) {

      var elem = document.getElementById(elemId);
      if(elem && document.createEvent) {
          var evt = document.createEvent("MouseEvents");
          evt.initEvent("click", true, false);
          elem.dispatchEvent(evt);
          }
      }

      (function() {

          var URL = window.URL || window.webkitURL;

              var input = document.querySelector('#modal_photo_upload');
              var preview = document.querySelector('#modal_photo');

          // When the file input changes, create a object URL around the file.
          input.addEventListener('change', function () {
              preview.src = URL.createObjectURL(this.files[0]);
              $("#nama_foto").val(URL.createObjectURL(this.files[0]));
//                foto_src = URL.createObjectURL(this.files[0]);
//                alert(URL.createObjectURL(this.files[0]));
          });

          // When the image loads, release object URL
          preview.addEventListener('load', function () {
              URL.revokeObjectURL(this.src);

//                alert(this.src);
          });
      })();

    </script>
