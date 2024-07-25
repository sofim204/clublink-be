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
		<li>Free Produk</li>
		<li>Edit</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="fedit" action="<?=base_url_admin('ecommerce/bulksale/edit/'); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>Data Utama</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="ieb_kategori_id">Kategori Produk</label>
					<select id="ieb_kategori_id" name="b_kategori_id" class="form-control">
						<option value="null"> - </option>
						<?php if(isset($kategori)){ foreach($kategori as $kat){ ?>
						<?php if(isset($kat->id)){ ?>
						<option value="<?php echo $kat->id; ?>"><?php echo $kat->nama; ?></option>
						<?php if(count($kat->childs)){ foreach($kat->childs as $kc){ ?>
						<option value="<?php echo $kc->id; ?>">--&nbsp;<?php echo $kc->nama; ?></option>
						<?php }}}}} ?>
					</select>
				</div>
				<div class="col-md-4">
					<label for="buser_search">Owner</label>
					<div class="input-group">
						<input id="ieb_user_fnama" name="" type="text" class="form-control disabled" value="<?=$user->fnama?>" />
						<input id="ieb_user_id" name="b_user_id" type="hidden" value="<?=$user->id?>" />
						<span class="input-group-btn">
						  <button id="buser_search" type="button" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
						</span>
					</div>
				</div>
				<div class="col-md-4">
					<label for="ieb_kondisi_id">Condition *</label>
					<select id="ieb_kondisi_id" name="b_kondisi_id" class="form-control" required>
						<option value="">--select--</option>
						<?php if(isset($kondisi)){ foreach($kondisi as $kon){ ?>
						<option value="<?=$kon->id?>"><?=$kon->nama?></option>
						<?php }} ?>
					</select>
				</div>
			</div>
			<div class="form-group"><div class="col-md-12"><br></div></div>
			<div class="form-group">
				<div class="col-md-12">
					<label class="" for="ienama">Nama Produk *</label>
					<input id="ienama" type="text" name="nama" class="form-control" minlength="1" placeholder="Nama Produk" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Pricing</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="ieharga_jual">Price*</label>
					<input id="ieharga_jual" type="text" class="form-control" placeholder="Price" value="0" required />
					<input id="iehharga_jual" type="hidden" name="harga_jual" class="form-control" placeholder="harga jual" value="0" />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Packaging</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="ieberat_id">Weight *</label>
					<select id="ieberat_id" name="b_berat_id" class="form-control" required>
						<option value="">--select--</option>
						<?php if(isset($berat)){ foreach($berat as $br){ ?>
						<option value="<?=$br->id?>"><?=$br->nama?></option>
						<?php }} ?>
					</select>
				</div>
				<div class="col-md-3">
					<label for="iestok">Stok</label>
					<input id="iestok" name="stok" type="text" class="form-control" value="0" />
				</div>

				<!-- By Donny Dennison - 27 july 2020 14:24 -->
				<!-- change length to depth -->
				<!-- <div class="col-md-2">
					<label for="iedimension_long">Long</label>
					<input id="eidimension_long" name="dimension_long" type="text" class="form-control" value="" />
				</div>
				<div class="col-md-2">
					<label for="iedimension_width">Width</label>
					<input id="iedimension_width" name="dimension_width" type="text" class="form-control" value="" />
				</div> -->
				<div class="col-md-2">
					<label for="iedimension_width">Width</label>
					<input id="iedimension_width" name="dimension_width" type="text" class="form-control" value="" />
				</div>
				<div class="col-md-2">
					<label for="iedimension_long">Depth</label>
					<input id="eidimension_long" name="dimension_long" type="text" class="form-control" value="" />
				</div>
				<div class="col-md-2">
					<label for="iedimension_height">Height</label>
					<input id="iedimension_height" name="dimension_height" type="text" class="form-control" value="" />
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
						<label class="control-label" for="iedeskripsi_singkat">Summary</label>
						<textarea id="iedeskripsi_singkat" name="deskripsi_singkat" class="ckeditor" rows="5"></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-12">
						<label class="control-label" for="iedeskripsi">Description</label>
						<textarea id="iedeskripsi" name="deskripsi" class="ckeditor" rows="5"></textarea>
					</div>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Misc</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-2">
					<label for="ieis_free_shipping">Free Shipping?</label>
					<select id="ieis_free_shipping" name="is_free_shipping" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
				<div class="col-md-2">
					<label for="ieis_include_delivery_cost">Inc Delivery Cost?</label>
					<select id="ieis_include_delivery_cost" name="is_include_delivery_cost" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
				<div class="col-md-2">
					<label for="ieis_featured">On Homepage?</label>
					<select id="ieis_featured" name="is_featured" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
				<div class="col-md-2">
					<label for="ieis_published">Pubished?</label>
					<select id="ieis_published" name="is_published" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
				<div class="col-md-2" style="display:none;">
					<label for="ieis_visible">Visibility</label>
					<select id="ieis_visible" name="is_visible" class="form-control">
						<option value="1">Visible</option>
						<option value="0">Hide</option>
					</select>
				</div>
				<div class="col-md-2">
					<label for="ieis_active">Is Active?</label>
					<select id="ieis_active" name="is_active" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
		</div>


		<div class="block full row">
			<div class="block-title">
				<div class="block-options pull-right">
					<button id="bimage_upload" href="#" class="btn btn-alt btn-sm btn-default" data-toggle="tooltip" title="Pilih gambar" data-original-title="Upload"><i class="fa fa-upload"></i> Upload</button>
				</div>
				<h2><strong>Image(s)</strong></h2>
			</div>
			<div id="dimage_list" class="row media-manager">
				<?php if(isset($produk->fotos)){ foreach($produk->fotos as $foto){ ?>
					<div id="kartu-<?=$foto->id?>" class="col-md-3">
						<div class="kartu">
							<div class="gambar">
								<img src="<?=base_url($foto->url)?>" class="img-responsive" />
							</div>
							<div class="teks">
								<div class="btn-group">
									<a href="#" class="btn btn-info btn-main-gambar" data-id="<?=$foto->id?>"><i class="fa fa-file-image-o"></i> Set Cover</a>
									<a href="#" class="btn btn-danger btn-remove-gambar" data-id="<?=$foto->id?>"><i class="fa fa-trash"></i> Remove</a>
								</div>

							</div>
						</div>
					</div>
				<?php }} ?>
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
						<button type="submit" value="" class="btn btn-success"><i class="fa fa-save"></i> Save Changes</button>
					</div>
				</div>
			</div>
		</div>
	</form>
	<!-- END Content -->
</div>
