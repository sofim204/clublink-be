<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin('ecommerce/produkwanted/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>E-Commerce</li>
		<li>Product Wanted</li>
		<li>Edit</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="fedit" action="<?=base_url_admin('ecommerce/produkwanted/edit/'); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="form-group">
				<div class="col-md-12">
					<label for="buser_search">User</label>
					<div class="input-group">
						<input id="ieb_user_fnama" name="" type="text" class="form-control disabled" value="<?=$user->id?> - <?=$user->fnama?>" />
						<input id="ieb_user_id" name="b_user_id" type="hidden" value="<?=$user->id?>" />
						<span class="input-group-btn">
						  <button id="buser_search" type="button" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
						</span>
					</div>
				</div>
				<div class="col-md-12">
					<label class="control-label" for="iekeyword_text">Keyword Text *</label>
					<input id="iekeyword_text" name="keyword_text" class="form-control" required/>
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
						<button type="submit" value="" class="btn btn-success"><i class="fa fa-save"></i> Save Changes</button>
					</div>
				</div>
			</div>
		</div>
	</form>
	<!-- END Content -->
</div>
