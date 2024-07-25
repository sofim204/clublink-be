<style>
	.text-left {
		text-align: left !important;
	}
</style>

<!-- modal option -->
<div id="modal_option" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Option</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="adetail" href="#" class="btn btn-info text-left">
							<i class="fa fa-info-circle"></i> Detail
						</a>
					</div>
				</div>
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					</div>
				</div>
				<!-- END Modal Body -->
			</div>
		</div>
	</div>
</div>



<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Demand Product Form</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<legend>Product</legend>
						<div class="form-group">
							<div class="col-md-4">
								<label for="iutype">Product Type*</label>
								<select id="iutype" name="utype" class="form-control">
									<option value="utama">Main Product</option>
									<option value="variasi">Variation Product</option>
								</select>
							</div>
							<div class="col-md-4">
								<label for="ib_kategori_id">Product Category</label>
								<select id="ib_kategori_id" name="b_kategori_id" class="form-control">
									<option value="null"> - </option>
									<?php if(isset($kategori)){ foreach($kategori as $kat){ ?>
									<option value="<?php echo $kat->id; ?>"><?php echo $kat->nama; ?></option>
									<?php if(count($kat->childs)){ foreach($kat->childs as $kc){ ?>
									<option value="<?php echo $kc->id; ?>">--&nbsp;<?php echo $kc->nama; ?></option>
									<?php }}}} ?>
								</select>
							</div>
							<div class="col-md-4">
								<label style="display:none" for="isku">SKU*</label>
								<input id="isku" type="text" name="sku" class="form-control" minlength="2" maxlength="20" placeholder="huruf, angka, titik, strip(-)" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="inama">Product Name</label>
								<input id="inama" type="text" name="nama" class="form-control" minlength="1" placeholder="Nama Produk" required />
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- modal edit -->
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Edit</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label for="ieutype">Product Type*</label>
								<select id="ieutype" name="utype" class="form-control">
									<option value="utama">Main Product</option>
									<option value="variasi">Variation Product</option>
								</select>
								<input id="ieid" name="id" type="hidden" value="" />
							</div>
							<div class="col-md-4">
								<label for="ieb_kategori_id">Product Category</label>
								<select id="ieb_kategori_id" name="b_kategori_id" class="form-control">
									<option value="null"> - </option>
									<?php if(isset($kategori)){ foreach($kategori as $kat){ ?>
									<option value="<?php echo $kat->id; ?>"><?php echo $kat->nama; ?></option>
									<?php if(count($kat->childs)){ foreach($kat->childs as $kc){ ?>
									<option value="<?php echo $kc->id; ?>">--&nbsp;<?php echo $kc->nama; ?></option>
									<?php }}}} ?>
								</select>
							</div>
							<div class="col-md-4">
								<label style="display:none" for="iesku">SKU*</label>
								<input id="iesku" type="text" name="sku" class="form-control" minlength="2" maxlength="20" placeholder="huruf, angka, titik, strip(-)" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="ienama">Product Name</label>
								<input id="ienama" type="text" name="nama" class="form-control" minlength="1" placeholder="Nama Produk" required />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Property</legend>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="ieslug">SLUG</label>
								<input id="ieslug" type="text" name="slug" class="form-control" minlength="1" placeholder="Slug" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<label for="ieukuran">Size*</label>
								<select id="ieukuran" name="ukuran" class="form-control">
									<option value="null"> - </option>
								</select>
							</div>
							<div class="col-md-4">
								<label for="iewarna">Color*</label>
								<select id="iewarna" name="warna" class="form-control">
									<option value="null"> - </option>
								</select>
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Price</legend>
						<div class="form-group">
							<div class="col-md-4">
								<label for="ieharga_beli">Purchase Price</label>
								<input id="ieharga_beli" name="harga_beli" type="text" class="form-control" minlength="1" placeholder="harga beli" />
							</div>
							<div class="col-md-4">
								<label for="ieharga_jual">Selling Price*</label>
								<input id="ieharga_jual" type="text" name="harga_jual" class="form-control" placeholder="harga jual" required />
							</div>
							<div class="col-md-4">
								<label for="ieharga_retail">Retail Price</label>
								<input id="ieharga_retail" type="text" name="harga_retail" class="form-control" placeholder="harga retail" />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Promotion / Discount</legend>
						<div class="form-group">
							<div class="col-md-4">
								<label for="iediskon_harga">Discount Price</label>
								<input id="iediskon_harga" name="diskon_harga" type="text" class="form-control" value="0" />
							</div>
							<div class="col-md-4">
								<label for="iediskon_persen">Discount Persentage (%)</label>
								<input id="iediskon_persen" name="diskon_persen" type="text" class="form-control" value="0" />
							</div>
							<div class="col-md-4">
								<label for="iediskon_expired">Discount Expired</label>
								<input id="iediskon_expired" type="text" name="diskon_expired" class="form-control input-datepicker-close" data-date-format="yyyy-mm-dd" placeholder="TTTT-BB-HH" />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Packaging</legend>
						<div class="form-group">
							<div class="col-md-4">
								<label for="ieberat">Packing weight (pound) *</label>
								<input id="ieberat" name="berat" type="text" class="form-control" value="" required />
							</div>
							<div class="col-md-4">
								<label for="iestok">Stock</label>
								<input id="iestok" name="stok" type="text" class="form-control" value="0" />
							</div>
							<div class="col-md-4">
								&nbsp;
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Product Description</legend>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iedeskripsi_singkat">Short Description</label>
								<textarea id="iedeskripsi_singkat" name="deskripsi_singkat" class="ckeditor" rows="5"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iedeskripsi">Full Description</label>
								<textarea id="iedeskripsi" name="ideskripsi" class="ckeditor" rows="5"></textarea>
							</div>
						</div>
					</fieldset>
					<fieldset><legend>Additional option</legend>
						<div class="form-group">
							<div class="col-md-3">
								<label for="iereview">Review</label>
								<input id="iereview" name="review" type="text" class="form-control" value="0" />
							</div>
							<div class="col-md-3">
								<label for="ierating">Rating</label>
								<input id="ierating" name="rating" type="text" class="form-control" value="0" />
							</div>
							<div class="col-md-3">
								<label for="ieterjual">Sold</label>
								<input id="ieterjual" name="terjual" type="text" class="form-control" value="0" />
							</div>
							<div class="col-md-3">
								<label for="iedilihat">Viewed</label>
								<input id="iedilihat" name="dilihat" type="text" class="form-control" value="0" />
							</div>
						</div>
					</fieldset>
					<fieldset><legend>important</legend>
						<div class="form-group">
							<div style="display:none" class="col-md-3">
								<label for="ieis_can_wait">Bisa PO?</label>
								<select id="ieis_can_wait" name="iis_can_wait" class="form-control">
									<option value="0">No</option>
									<option value="1">Yes</option>
								</select>
							</div>
							<div style="display:none" class="col-md-3">
								<label for="iewaiting_day">Proses PO (Hari)</label>
								<input id="iewaiting_day" name="waiting_day" type="text" class="form-control" value="0" />
							</div>
							<div style="display:none" class="col-md-3">
								<label for="ieis_visible">Dapat Dilihat</label>
								<select id="ieis_visible" name="is_visible" class="form-control">
									<option value="1">Iya</option>
									<option value="0">Tidak</option>
								</select>
							</div>
							<div class="col-md-3">
								<label for="ieis_active">Active</label>
								<select id="ieis_active" name="is_active" class="form-control">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="bhapus" type="button" class="btn btn-sm btn-warning">Delete</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>
