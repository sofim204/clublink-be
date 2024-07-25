<?php
if(!isset($homepage_data->block1_enable)) $homepage_data->block1_enable = 1;
if(!isset($homepage_data->block1_mode)) $homepage_data->block1_mode = 1;
if(!isset($homepage_data->block1_items)) $homepage_data->block1_items = '';
if(!isset($homepage_data->block1_items_array)) $homepage_data->block1_items_array = array();
?>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<a id="asimpan" href="#" class="btn btn-success"><i class="fa fa-save"></i> Simpan</a>
					<a id="acompile" href="#" class="btn btn-info"><i class="fa fa-refresh"></i> Compile</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>CMS</li>
		<li><a href="<?=base_url_admin('cms/homepage/')?>">Homepage</a></li>
		<li>Block 2</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Block1 -->
	<div class="block full">
		<div class="block-title">
			<div class="block-options pull-right">
				<button href="#" class="btn btn-alt btn-sm btn-default btn-media-tambah" data-toggle="tooltip" title="Pilih gambar" data-original-title="Pilih gambar" data-media-div="block1_items" data-media-caption="1" data-media-url="1"><i class="fa fa-plus"></i></button>
			</div>
			<h2><strong>Homepage Block-2 option <small></small></strong></h2>
		</div>
		<div class="row" style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px #ededed solid;">
			<div class="col-md-9">&nbsp;</div>
			<div class="col-md-3">
				<select id="ihp_block2_enable" class="form-control">
					<option value="1" <?php if(!empty($homepage_data->block2_enable)) echo 'selected'; ?>>Aktif</option>
					<option value="0" <?php if(empty($homepage_data->block2_enable)) echo 'selected'; ?>>Tidak Aktif</option>
				</select>
			</div>
			<div class="col-md-12">
				<textarea id="ihp_block2_teks" class="ckeditor"><?=$this->seme_purifier->richtext($homepage_data->block2_teks)?></textarea>
			</div>
		</div>
	</div>
	<!-- END Block1 -->

</div>
