<style>
	.btn-back {
        width: 85px;
        cursor: pointer;
        background: #F9F5F5;
        border: 1px solid #999;
        outline: none;
		color: #222121;
        transition: .3s ease;
    }

    .btn-back:hover {
        transition: .3s ease;
        background: #DD8A0D;
        border: 1px solid transparent;
        color:#FFF;
    }
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin('ecommerce/kategori_automotive/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Automotive Categories</li>
		<li>Edit</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>General</strong></h2>
			</div>
			<!-- START by Muhammad Sofi 22 December 2021 10:00 | remark unused code -->
			<!-- <div class="form-group" style="display: none;">
				<div class="col-md-4">
					<label for="ieutype">Level*</label>
					<select id="ieutype" name="utype" class="form-control">
						<option value="kategori" data-kode="1">Main Category</option>
						<option value="kategori_sub" data-kode="2">Sub Category</option>
						<option value="kategori_sub_sub" data-kode="3">Sub Category of Sub Category</option>
					</select>
				</div>
				<div class="col-md-4">
					<label for="ieb_kategori_id">Parent Categories</label>
					<select id="ieb_kategori_id" name="parent_b_kategori_id" class="form-control">
						<option value="null"> - </option>
						<?php if(isset($kategori)){ foreach($kategori as $kat){ ?>
						<?php if(isset($kat->id)){ ?>
						<option value="<?=$kat->id; ?>" ><?=$kat->nama; ?></option>
						<?php if(count($kat->childs)){ foreach($kat->childs as $kc){ ?>
						<option value="<?=$kc->id; ?>" >--&nbsp;<?=$kc->nama; ?></option>
						<?php }}}}} ?>
					</select>
				</div>
			</div>
			<div class="form-group" style="display: none;"><div class="col-md-12">&nbsp;</div></div> -->
			<!-- END by Muhammad Sofi 22 December 2021 10:00 | remark unused code -->
			<div class="form-group">
				<div class="col-md-12">
					<label class="" for="ienama">Name</label>
					<input id="ienama" type="text" name="nama" class="form-control" minlength="1" placeholder="Brand Name" required />
				</div>
			</div>
		</div>
		<div class="block full row" style="display: none;">
			<div class="block-title">
				<h2><strong>Description</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<label class="control-label" for="iedeskripsi"></label>
					<textarea id="iedeskripsi" name="deskripsi" class="ckeditor" rows="5"></textarea>
				</div>
			</div>
		</div>
		<div class="block full row">
			<div class="block-title">
				<h2><strong>Misc</strong></h2>
			</div>
			<div class="form-group">
				<!--<div class="col-md-4">
					<label for="ieprioritas">Priority* <small>(0 high-99 low)</small></label>
					<input id="ieprioritas" name="prioritas" type="text" class="form-control" value="0" />

				</div>-->

				<!-- id=ie(nama field table) -->
				<div class="col-md-3">
					<label class="" for="ieprioritas">Priority (1-100)</label>
					<select id="ieprioritas" name="prioritas" class="form-control" required>
						<?php for ($i=1; $i <= 100; $i++) { ?>	
							<option value="<?= $i ?>"><?= $i ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-md-3">
					<label class="" for="ieprioritas_indonesia">Priority Indo (1-100)</label>
					<select id="ieprioritas_indonesia" name="prioritas_indonesia" class="form-control" required>
						<?php for ($i=1; $i <= 100; $i++) { ?>	
							<option value="<?= $i ?>"><?= $i ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-md-3">
					<label for="ieparent_b_kategori_id">Automotive Type</label>
					<select id="ieparent_b_kategori_id" name="parent_b_kategori_id" class="form-control">
						<option value="32">Car</option>
						<option value="33">Motorcycle</option>
					</select>
				</div>
				<!-- by Muhammad Sofi 22 December 2021 10:00 | remark unused code -->
				<!-- <div class="col-md-4" style="display:none;">
					<label for="ieis_visible">Visibility</label>
					<input type="hidden" name="is_visible" value="1" />
					<select id="ieis_visible" name="" class="form-control">
						<option value="1">Visible</option>
						<option value="0">Hide</option>
					</select>
				</div> -->
				<div class="col-md-3">
					<label for="ieis_active">Active</label>
					<select id="ieis_active" name="is_active" class="form-control">
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
			<div class="form-group form-actions">
				<div class="col-xs-12 text-right">
					<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Changes</button>
				</div>
			</div>
		</div>
	</form>
</div>
