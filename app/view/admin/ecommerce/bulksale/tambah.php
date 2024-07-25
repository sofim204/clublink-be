<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin('ecommerce/bulksale/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Kembali</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li><a href="<?=base_url("ecommerce/bulksale/")?>">Free Product</a></li>
		<li>New</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="ftambah" action="<?=base_url_admin('ecommerce/bulksale/tambah/'); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>General</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="ib_kategori_id">Category</label>
					<select id="ib_kategori_id" name="b_kategori_id" class="form-control">
						<option value="null" data-kode=""> - </option>
						<?php if(isset($kategori)){ foreach($kategori as $kat){ ?>
						<?php if(isset($kat->id)){ ?>
						<option value="<?=$kat->id; ?>" data-kode="<?=$kat->kode?>"><?=$kat->nama; ?></option>
						<?php if(count($kat->childs)){ foreach($kat->childs as $kc){ ?>
						<option value="<?=$kc->id; ?>" data-kode="<?=$kc->kode?>">--&nbsp;<?=$kc->nama; ?></option>
						<?php }}}}} ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="ib_kategori_id">Owner</label>
					<div class="input-group">
						<input id="ib_user_fnama" type="text" class="form-control disabled" value="-" />
						<input id="ib_user_id" name="b_user_id" type="hidden" value="null" />
						<span class="input-group-btn">
						  <button id="buser_search" type="button" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
						</span>
					</div>
				</div>
			</div>
			<div class="form-group"><div class="col-md-12"><br></div></div>
			<div class="form-group">
				<input id="isku" name="sku" type="hidden" value="<?=date("ymdhis").rand(0,999)?>" />
				<div class="col-md-12">
					<label class="" for="inama">Name *</label>
					<input id="inama" type="text" name="nama" class="form-control" minlength="1" placeholder="Nama Produk" required />
				</div>
			</div>
		</div>

		<div class="block full row" style="display: none;">
			<div class="block-title">
				<div class="block-options pull-right">
					<a href="#" class="btn btn-alt btn-sm btn-default btn-hidden-block" >
						<i class="fa fa-minus"></i>
					</a>
				</div>
				<h2><strong>SEO</strong></h2>
			</div>
			<div class="form-group" style="display:none;">
				<div class="col-md-4">
					<label class="" for="imtitle">Meta Title</label>
					<input id="imtitle" type="text" name="mtitle" class="form-control" minlength="1" maxlength="90" />
				</div>
				<div class="col-md-4">
					<label class="" for="islug">SLUG*</label>
					<input id="islug" type="text" name="slug" class="form-control" minlength="1" placeholder="Slug" required />
				</div>
				<div class="col-md-4">
					<label class="" for="imkeyword">Meta Keyword</label>
					<input id="imkeyword" type="text" name="mkeyword" class="form-control" minlength="1" placeholder="" />
				</div>
				<div class="col-md-12">
					<label for="iukuran">Meta Description</label>
					<textarea id="imdescription" name="mdescription" class="form-control" maxlength="160"></textarea>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Pricing</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="iharga_jual">Price*</label>
					<input id="iharga_jual" type="text" class="form-control" placeholder="harga jual" value="0" required />
					<input id="ihharga_jual" type="hidden" name="harga_jual" class="form-control" placeholder="harga jual" value="0" />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Packaging</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="iberat">Weight *</label>
					<select id="iberat" name="berat" class="form-control" required>
						<?php if(isset($berat)){ foreach($berat as $br){ ?>
						<option value="<?=$br->nilai?>"><?=$br->nama?></option>
						<?php }} ?>
					</select>
				</div>
				<div class="col-md-4">
					<label for="istok">Stok</label>
					<input id="istok" name="stok" type="text" class="form-control" value="0" />
				</div>
				<div class="col-md-4">

					<!-- By Donny Dennison - 27 july 2020 14:24 -->
					<!-- change length to depth -->
					<!-- <label for="idimensi">Dimension (LxWxH)</label> -->
					<label for="idimensi">Dimension (WxDxH)</label>

					<input id="idimensi" name="dimensi" type="text" class="form-control" value="" />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<div class="block-options pull-right">
					<a href="#" class="btn btn-alt btn-sm btn-default btn-hidden-block" >
						<i class="fa fa-minus"></i>
					</a>
				</div>
				<h2><strong>Description</strong></h2>
			</div>
			<div class="form-group" style="display:none;">
				<div class="form-group" style="display:none;">
					<div class="col-md-12">
						<label class="control-label" for="ideskripsi_singkat">Summary</label>
						<textarea id="ideskripsi_singkat" name="deskripsi_singkat" class="ckeditor" rows="5"></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-12">
						<label class="control-label" for="ideskripsi">Description</label>
						<textarea id="ideskripsi" name="deskripsi" class="ckeditor" rows="5"></textarea>
					</div>
				</div>
			</div>
		</div>


		<div class="block full row">
			<div class="block-title">
				<div class="block-options pull-right">
					<button id="bimageadd" type="button" class="btn btn-alt btn-sm btn-default"><i class="fa fa-plus"></i> Add Image</button>
				</div>
				<h2><strong>Image(s)</strong></h2>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div id="dimage_list" class="row media-manager"></div>
				</div>
			</div>
		</div>

		<div class="block full row" style="display: none;">
			<div class="block-title">
				<h2><strong>Lain-lain</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="ireview">Review</label>
					<input id="ireview" name="review" type="text" class="form-control" value="0" />
				</div>
				<div class="col-md-3">
					<label for="irating">Rating</label>
					<input id="irating" name="rating" type="text" class="form-control" value="0" />
				</div>
				<div class="col-md-3">
					<label for="iterjual">Terjual</label>
					<input id="iterjual" name="terjual" type="text" class="form-control" value="0" />
				</div>
				<div class="col-md-3">
					<label for="idilihat">Dilihat</label>
					<input id="idilihat" name="dilihat" type="text" class="form-control" value="0" />
				</div>
			</div>
		</div>

		<div class="block full row" style="display: none;">
			<div class="block-title">
				<div class="block-options pull-right">
					<a href="#" class="btn btn-alt btn-sm btn-default btn-hidden-block" >
						<i class="fa fa-minus"></i>
					</a>
				</div>
				<h2><strong>Custom</strong></h2>
			</div>
			<div class="form-group" style="display:none;">
				<div class="col-md-3">
					<label for="iis_customize">Bisa Custom?</label>
					<select id="iis_customize" name="is_customize" class="form-control">
						<option value="0">Tidak</option>
						<option value="1">Iya</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="imoq_custom">MOQ Custom</label>
					<input id="imoq_custom" name="moq_custom" type="text" class="form-control" value="50" />
				</div>
				<div class="col-md-3">
					<label for="iis_can_wait">Bisa PO?</label>
					<select id="iis_can_wait" name="is_can_wait" class="form-control">
						<option value="0">Tidak</option>
						<option value="1">Iya</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="iwaiting_day">Proses PO (Hari)</label>
					<input id="iwaiting_day" name="waiting_day" type="text" class="form-control" value="0" />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Address</strong></h2>
			</div>
			<div class="form-group" >
				<div class="col-md-12">
					<label for="ialamat" class="control-label">Address</label>
					<input id="ialamat" name="alamat" type="text" class="form-control" value="" />
				</div>
				<div class="col-md-6">
					<label for="ikecamatan" class="control-label">District</label>
					<input id="ikecamatan" name="kecamatan" type="text" class="form-control" value="" />
				</div>
				<div class="col-md-6">
					<label for="ikabkota" class="control-label">City</label>
					<input id="ikabkota" name="kabkota" type="text" class="form-control" value="" />
				</div>
				<div class="col-md-6">
					<label for="iprovinsi" class="control-label">Province</label>
					<input id="iprovinsi" name="provinsi" type="text" class="form-control" value="" />
				</div>
				<div class="col-md-6">
					<label for="inegara" class="control-label">Country</label>
					<input id="inegara" name="negara" type="text" class="form-control" value="" />
				</div>
				<div class="col-md-6">
					<label for="ikodepos" class="control-label">Postal Code</label>
					<input id="ikodepos" name="kodepos" type="text" class="form-control" value="" />
				</div>
				<div class="col-md-12"><br /></div>
				<div class="col-md-6">
					<label for="ilatitude">Latitude</label>
					<input id="ilatitude" name="latitude" type="text" class="form-control" value="" />
				</div>
				<div class="col-md-6">
					<label for="ilongitude">Longitude</label>
					<input id="ilongitude" name="longitude" type="text" class="form-control" value="" />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Properties</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="ikondisi">Condition</label>
					<select id="ikondisi" name="kondisi" class="form-control">
						<?php if(isset($kondisi)){ foreach($kondisi as $kon){ ?>
						<option value="<?=$kon->nilai?>"><?=$kon->nama?></option>
						<?php }} ?>
						<option value="new">New</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="iis_visible">Visibility</label>
					<select id="iis_visible" name="is_visible" class="form-control">
						<option value="1">Visible</option>
						<option value="0">Hide</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="iis_featured">Featured</label>
					<select id="iis_featured" name="is_featured" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="iis_active">Active</label>
					<select id="iis_active" name="is_active" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Action</strong></h2>
			</div>
			<div class="row">
				<div class="col-md-8">&nbsp;</div>
				<div class="col-md-4 text-right">
					<div class="btn-group">
						<button type="submit" value="" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
					</div>
				</div>
			</div>
		</div>
	</form>
	<!-- END Content -->
</div>
