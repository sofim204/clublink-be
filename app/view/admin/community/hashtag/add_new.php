
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin('community/hashtag/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Community</li>
		<li>Hashtag</li>
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
				<div class="col-md-12">
					<label class="" for="inama">Name *</label>
					<input id="inama" type="text" name="name" class="form-control" minlength="1" placeholder="Hashtag" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Misc</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
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
			<div class="form-group form-actions">
				<div class="col-xs-12 text-right">
					<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
				</div>
			</div>
		</div>

	</form>
</div>
