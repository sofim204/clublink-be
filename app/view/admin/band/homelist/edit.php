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
					<a id="aback" href="<?=base_url_admin('band/homelist/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Band</li>
		<li>Event Registration</li>
		<li>Edit</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>Name & Type</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label class="" for="ieenglish">English</label>
					<input id="ieenglish" type="text" name="english" class="form-control" minlength="1" autocomplete="off" />
				</div>
				<div class="col-md-4">
					<label class="" for="ieindonesia">Indonesia</label>
					<input id="ieindonesia" type="text" name="indonesia" class="form-control" minlength="1" autocomplete="off" />
				</div>
				<div class="col-md-12">
					&nbsp;
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label class="" for="ietype">Type</label>
					<input id="ietype" type="text" name="type" class="form-control" minlength="1" autocomplete="off" />
				</div>
				<!-- <div class="col-md-4 show-subcategories hidden">
					<label for="select_sub_category" style="margin-bottom: 7px;">Select Sub Category</label>
					<br>
					<select id="select_sub_category" name="i_group_sub_category_id" class="form-control" style="width: 272px;">
						<option value="">-- Select Sub Category --</option>
					</select>
				</div> -->
				<div class="col-md-4 show-subcategories hidden">
					<label class="" for="input_subcategory">Sub Category</label>
					<input id="iei_group_sub_category_id" type="hidden" name="i_group_sub_category_id" class="form-control" minlength="1" autocomplete="off" />
					<input id="iesub_category_name" type="text" name="" class="form-control" minlength="1" autocomplete="off" />
				</div>
			</div>
			<input type="hidden" id="ieurl" name="url" />
		</div>
		<!-- <div class="block full row image-banner-container hidden">
			<div class="block-title">
				<h2><strong>Image Banner</strong></h2> <h7><i>(image extentions : ".jpg", ".png", ".jpeg")</i></h7>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<input id="" type="file" name="" class="form-control" accept="image/x-png,image/jpeg,image/jpg"  />
				</div>
				
			</div>
		</div> -->
		<div class="block full row">
			<div class="block-title">
				<h2><strong>Priority & Active</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-2">
					<label class="" for="iprioritas">English *</label>
					<input id="ieprioritas" type="number" name="prioritas" class="form-control" autocomplete="off" min="0" max="100" />
				</div>
				<div class="col-md-2">
					<label class="" for="iprioritas">Indonesia *</label>
					<input id="ieprioritas_indonesia" type="number" name="prioritas_indonesia" class="form-control" autocomplete="off" min="0" max="100" />
				</div>
				<div class="col-md-2">
					<label for="ieis_active">Active *</label>
					<select id="ieis_active" name="is_active" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
				<div class="col-md-3">
					<label>&nbsp;</label>
					<div>
						<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Changes</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
