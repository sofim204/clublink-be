<style>
#copasmediabutton, .thmb-prev {
  cursor: pointer;
}
#shTable_wrapper > div.dt-buttons {
  background: transparent;
}
#shTable_wrapper > div.dt-buttons > a {
  background: transparent;
  border: none;
  border-radius: 0;
  display: inline-block;
  padding: 6px 12px;
  margin-bottom: 0;
  font-size: 14px;
  font-weight: 400;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  background-image: none;
  border: 1px solid transparent;
  border-radius: 4px;
  background-color: #31b0d5;
  border-color: #269abc;
  color: #fff;
}
.btn.btn-secondary {
  background-color: #ededed;
  color: #fff;
}
option[disabled] {
  background-color: #ececec;
  color: #b0b0b0;
  font-style: italic;
}
.modal { overflow: auto !important; }
</style>
<div id="page-content">
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?php echo base_url_admin('ecommerce/produk/'); ?>" class="btn btn-info"><i class="fa fa-chevron-left"></i> Kembali</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Produk</li>
		<li>Detail</li>
	</ul>
	<form id="fadd" method="post" action="<?php echo base_url("api/galeri/add/"); ?>" class="form-horizontal">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>Formulir Utama</strong></h2>
			</div>

      <div class="form-group">
        <div class="col-md-12">
          <label for="ietitle" class="control-label">Judul</label>
          <input type="text" id="ietitle" name="title" class="form-control" value="" />
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-12">
          <label for="iekategori" class="control-label">Kategori</label>
          <input type="text" id="iekategori" name="kategori" class="form-control" value="" />
        </div>
      </div>

      <div class="form-group">
        <div class="col-md-12">
          <label for="taecontent" class="control-label">Isi</label>
          <textarea id="taecontent" name="content" class="form-control mswrd" rows="15"></textarea>
        </div>
      </div>
      <div class="form-group " style=" padding:0.5em;">
        <div class="col-md-12">
          <div class="row" style="border: 1px #c0c0c0 solid;">

            <div class="col-md-12" style="background-color: rgba(0,0,0,0.2)">
              <div class="row">
                <div class="col-md-10">
                  <h4>Galeri Item</h4>
                </div>
                <div class="col-md-2" style="padding-top: 0.25em; padding-bottom: 0.25em;">
          				<button id="bgaleritambah" class="btn btn-primary btn-block">+ Galeri</button>
                </div>
              </div>

            </div>
            <div class="col-md-12" style="min-height: 30vh; padding-top: 0.5em;">
              <div id="dgaleri_items" class="row media-manager"></div>
      			</div>
          </div>

        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-12">
          <button type="submit" class="btn btn-success">Simpan</button>
        </div>
      </div>
		</div>
	</form>
</div>
