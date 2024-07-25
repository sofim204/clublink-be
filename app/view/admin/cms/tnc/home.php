
<!-- START by Muhammad Sofi 19 January 2022 12:05 | move layout tnc from edit to home -->
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6"></div>
			<div class="col-md-6">
				<div class="btn-group pull-right" style="margin-right:1em">
					<a id="" href="<?php echo base_url_admin('cms/tnc/tncMobile/2'); ?>" target="_blank" class="btn btn-primary pull-right"> RESULT INDO</a>
				</div>
				<div class="btn-group pull-right" style="margin-right:1em">
					<a id="" href="<?php echo base_url_admin('cms/tnc/tncMobile/1'); ?>" target="_blank" class="btn btn-primary pull-right"> RESULT</a>
				</div>
			</div>
		</div>
	</div>

	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>CMS</li>
		<li>Term and Condition</li>
	</ul>
	<!-- END Static Layout Header -->

	<div class="block full block-alt-noborder">
		<div class="row">
			<div class="col-md-6">
				<form id="tnc_form" method="post" action="<?=base_url_admin("cms/tnc/")?>" enctype="multipart/form-data" class="">
					<label for="itnc" class="control-label"><h4 id="text_content"><strong>Terms and Conditions</strong></h4></label>
					<div class="form-group">
						<textarea id="itnc" name="content" class="ckeditor"></textarea>
						<br />
						<div class="btn-group pull-right">
							<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Changes</button>
						</div>
					</div>
				</form>	
			</div>
			<div class="col-md-6">
				<form id="tnc_form_indonesia" method="post" action="<?=base_url_admin("cms/tnc/")?>" enctype="multipart/form-data" class="">
					<label for="itnc_indonesia" class="control-label"><h4 id="text_content"><strong>Syarat dan Ketentuan</strong></h4></label>
					<div class="form-group">
						<textarea id="itnc_indonesia" name="content" class="ckeditor"></textarea>
						<br />
						<div class="btn-group pull-right">
							<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Changes</button>
						</div>
					</div>
				</form>	
			</div>
		</div>
	</div>
</div>
<!-- END by Muhammad Sofi 19 January 2022 12:05 | move layout tnc from edit to home -->