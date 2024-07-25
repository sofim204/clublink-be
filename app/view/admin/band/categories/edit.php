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

	.hidden {
		display: none;
	}
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin('band/categories/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Band</li>
		<li><span class="title-subcategories hidden">Sub </span>Category</li>
		<li>Edit</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>General</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label class="" for="ienama">Name (English) *</label>
					<input id="ienama" type="text" name="nama" class="form-control" minlength="1" placeholder="Name" autocomplete="off" required />
				</div>
				<div class="col-md-4">
					<label class="" for="ieindonesia">Indonesia *</label>
					<input id="ieindonesia" type="text" name="indonesia" class="form-control" minlength="1" placeholder="Indonesia" autocomplete="off" required />
				</div>
			</div>	
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Priority</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label class="" for="ieprioritas">Priority *</label>
					<input id="ieprioritas" type="number" name="prioritas" class="form-control" placeholder="Prioritas" autocomplete="off" min="0" max="100" required />
				</div>
				<div class="col-md-4">
					<label class="" for="ieprioritas_indonesia">Indonesia Priority *</label>
					<input id="ieprioritas_indonesia" type="number" name="prioritas_indonesia" class="form-control" placeholder="Prioritas Indonesia" autocomplete="off" min="0" max="100" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Misc</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="ieis_visible">Visible *</label>
					<select id="ieis_visible" name="is_visible" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="ieis_active">Active *</label>
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