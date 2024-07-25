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
					<a id="aback" href="<?=base_url_admin('community/category/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li><a href="<?=base_url_admin("community/category/")?>">Categories</a></li>
		<li>Edit</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>General</strong></h2>
			</div>
			<div class="form-group" style="display: none;"><div class="col-md-12">&nbsp;</div></div>
			<div class="form-group">
				<div class="col-md-12">
					<label class="" for="ieindonesia">Name</label>
					<a id="btn_change_language" style="text-decoration: none; cursor: pointer; float: right;">change language</a>
					<input id="ieindonesia" type="text" name="indonesia" class="form-control" minlength="1" autocomplete="off" />
				</div>
			</div>
			<!-- by Muhammad Sofi 15 February 2022 17:23 | add column for multilanguage community category -->
			<div class="form-group" id="column_language" style="display: none;">
				<div class="col-md-12">
					<label class="" for="ienama">Name English</label>
					<input id="ienama" type="text" name="nama" class="form-control" minlength="1" autocomplete="off" />
				</div>
				<div class="col-md-12">
					<label class="" for="iekorea">Name Korea</label>
					<input id="iekorea" type="text" name="korea" class="form-control" minlength="1" autocomplete="off" />
				</div>
				<div class="col-md-12">
					<label class="" for="iethailand">Name Thailand</label>
					<input id="iethailand" type="text" name="thailand" class="form-control" minlength="1" autocomplete="off" />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Description</strong></h2>
			</div>
			<!-- <div class="form-group">
				<div class="col-md-12">
					<label class="control-label" for="iedeskripsi"></label>
					<textarea id="iedeskripsi" name="deskripsi" class="ckeditor" rows="5"></textarea>
				</div>
			</div> -->
			<div class="form-group">
				<div class="col-md-12">
					<label class="" for="iedeskripsi_indonesia">Description</label>
					<a id="btn_change_language_description" style="text-decoration: none; cursor: pointer; float: right;">change language</a>
					<input id="iedeskripsi_indonesia" type="text" name="deskripsi_indonesia" class="form-control" minlength="1" autocomplete="off" required />
				</div>
			</div>
			<!-- by Muhammad Sofi 15 February 2022 17:23 | add column for multilanguage community category -->
			<div class="form-group" id="column_language_description" style="display: none;">
				<div class="col-md-12">
					<label class="" for="iedeskripsi">Name English</label>
					<input id="iedeskripsi" type="text" name="deskripsi" class="form-control" minlength="1" autocomplete="off" />
				</div>
				<div class="col-md-12">
					<label class="" for="iedeskripsi_korea">Name Korea</label>
					<input id="iedeskripsi_korea" type="text" name="deskripsi_korea" class="form-control" minlength="1" autocomplete="off" />
				</div>
				<div class="col-md-12">
					<label class="" for="iedeskripsi_thailand">Name Thailand</label>
					<input id="iedeskripsi_thailand" type="text" name="deskripsi_thailand" class="form-control" minlength="1" autocomplete="off" />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Misc</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="ieprioritas">Priority* <small>(0 high-99 low)</small></label>
					<input id="ieprioritas" name="prioritas" type="text" class="form-control" value="0" />
				</div>
				<div class="col-md-3">
					<label for="ieprioritas_indonesia">Priority Indo* <small>(0 high-99 low)</small></label>
					<input id="ieprioritas_indonesia" name="prioritas_indonesia" type="text" class="form-control" value="0" />
				</div>
				<div class="col-md-3">
					<label for="ieis_visible">Is Visible</label>
					<select id="ieis_visible" name="is_visible" class="form-control">
						<option value="1">Visible</option>
						<option value="0">Hidden</option>
					</select>
				</div>
				<div class="col-md-4" style="display:none;">
					<label for="ieis_visible">Visibility</label>
					<input type="hidden" name="is_visible" value="1" />
					<select id="ieis_visible" name="" class="form-control">
						<option value="1">Visible</option>
						<option value="0">Hide</option>
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
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Action</strong></h2>
			</div>
			<div class="form-group form-actions">
				<div class="col-xs-12 text-right">
					<button type="submit"  class="btn btn-primary"><i class="fa fa-save"></i> Save Changes</button>
				</div>
			</div>
		</div>
	</form>
</div>
