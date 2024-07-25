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
		<li>Category</li>
		<li>New</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>General</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-6">
					<label class="" for="inama">Name *</label>
					<input id="inama" type="text" name="nama" class="form-control" minlength="1" placeholder="Category Name" autocomplete="off" required />
				</div>
				<div class="col-md-6">
					<label class="" for="iindonesia">Name Indo *</label>
					<input id="iindonesia" type="text" name="indonesia" class="form-control" minlength="1" placeholder="Nama Kategori" autocomplete="off" required />
				</div>
			</div>
		</div>
		<div class="block full row">
			<div class="block-title">
				<h2><strong>Image Icon *</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<input id="iimage_icon" name="image_icon" type="file" accept=".jpg, .jpeg, .png" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Image Cover *</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<input id="iimage_cover" name="image_cover" type="file" accept=".jpg, .jpeg, .png" />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Description</strong></h2>
			</div>
			<!-- <div class="form-group">
				<div class="col-md-12">
					<label class="control-label" for="ideskripsi"></label>
					<textarea id="ideskripsi" name="deskripsi" class="ckeditor" rows="5"></textarea>
				</div>
			</div> -->
			<!-- by Muhammad Sofi 21 January 2022 16:11 | change input description from textarea to input text-->
			<div class="form-group">
				<div class="col-md-6">
					<label class="" for="ideskripsi">Description *</label>
					<input id="ideskripsi" type="deskripsi" name="deskripsi" class="form-control" minlength="1" placeholder="Description" autocomplete="off" required />
				</div>
				<div class="col-md-6">
					<label class="" for="ideskripsi">Description Indo *</label>
					<input id="ideskripsi" type="deskripsi" name="deskripsi_indonesia" class="form-control" minlength="1" placeholder="Deskripsi" autocomplete="off" required />
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
				<div class="col-md-4">
					<label for="iprioritas_indonesia">Priority Indo* <small>(0 high-99 low)</small></label>
					<input id="iprioritas_indonesia" name="prioritas_indonesia" type="number" class="form-control" value="0" />
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
