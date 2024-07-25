
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin('community/emoji/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>Emoji</li>
		<li>New</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>General</strong></h2>
			</div>
			<div class="form-group" style="display: none;">
				<div class="col-md-4">
					<label for="iutype">Level*</label>
					<select id="iutype" name="utype" class="form-control">
						<option value="kategori" data-kode="1">Main Emoji</option>
						<option value="kategori_sub" data-kode="2">Sub Emoji</option>
						<option value="kategori_sub_sub" data-kode="3">Sub Emoji of Sub Emoji</option>
					</select>
				</div>
				<div class="col-md-4">
					<label for="ib_kategori_id">Parent</label>
					<select id="ib_kategori_id" name="b_kategori_id" class="form-control">
						<option value="null" data-kode=""> - </option>
						<?php if(isset($kategori)){ foreach($kategori as $kat){ ?>
						<?php if(isset($kat->id)){ ?>
						<option value="<?php echo $kat->id; ?>" data-kode="<?=$kat->kode?>"><?php echo $kat->nama; ?></option>
						<?php if(count($kat->childs)){ foreach($kat->childs as $kc){ ?>
						<option value="<?php echo $kc->id; ?>" data-kode="<?=$kc->kode?>">--&nbsp;<?php echo $kc->nama; ?></option>
						<?php }}}}} ?>
					</select>
				</div>
			</div>
			<div class="form-group" style="display: none;"><div class="col-md-12">&nbsp;</div></div>
			<div class="form-group">
				<div class="col-md-12">
					<label class="" for="inama">Name *</label>
					<input id="inama" type="text" name="nama" class="form-control" minlength="1" placeholder="Emoji Name" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Icon *</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<input id="iimage_icon" name="image_icon" type="file" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Description</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<label class="control-label" for="ideskripsi"></label>
					<textarea id="ideskripsi" name="deskripsi" class="ckeditor" rows="5"></textarea>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Misc</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="iprioritas">Priority* <small>(0 high-99 low)</small></label>
					<input id="iprioritas" name="prioritas" type="number" class="form-control" value="0" />
				</div>
				<!-- <div class="col-md-4" style="display:none;">
					<label for="iis_visible">Visibility</label>
					<input type="hidden" name="is_visible" value="1" />
					<select id="iis_visible" name="" class="form-control">
						<option value="1">Visible</option>
						<option value="0">Hide</option>
					</select>
				</div> -->
				<div class="col-md-4">
					<label for="iis_active">Active</label>
					<select id="iis_active" name="is_active" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
				<!-- Hidden Values -->
				<input type="hidden" name="is_visible" value="1" />
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Action</strong></h2>
			</div>
			<div class="form-group form-actions">
				<div class="col-xs-12 text-right">
					<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
				</div>
			</div>
		</div>

	</form>
</div>
