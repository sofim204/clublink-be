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
		<li>Ecommerce</li>
		<li>Homepage</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<div class="block full">
		<div class="block-title">
			<h2><strong>SEO Option</strong></h2>
		</div>
		<div class="row">
			<div class="col-md-12">
				<label for="ihpmtitle" class="control-label">Meta Title</label>
        <input type="text" id="ihpmtitle" class="form-control" placeholder="Meta Title Max 90 Char" value="<?=$homepage_data->mtitle?>" />
			</div>
			<div class="col-md-12">
				<label for="ihpmkeyword" class="control-label">Meta Keyword</label>
        <input type="text" id="ihpmkeyword" class="form-control" placeholder="Meta keyword" value="<?=$homepage_data->mkeyword?>" />
			</div>
			<div class="col-md-12">
				<label for="ihpmdescription" class="control-label">Meta Description</label>
        <input type="text" id="ihpmdescription" class="form-control" placeholder="Meta description" value="<?=$homepage_data->mdescription?>" />
			</div>
		</div>

	</div>
	<!-- END Content -->

	<!-- Slider -->
	<div class="block full">
		<div class="block-title">
			<div class="block-options pull-right">
				<button href="#" class="btn btn-alt btn-sm btn-default btn-media-tambah" data-toggle="tooltip" title="Pilih gambar" data-original-title="Pilih gambar" data-media-div="slider_items"><i class="fa fa-plus"></i></button>
			</div>
			<h2><strong>Slider option</strong></h2>
		</div>
		<div class="row" style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px #ededed solid;">
			<div class="col-md-9">&nbsp;</div>
			<div class="col-md-3">
				<select id="ihpslider_enable" class="form-control">
					<option value="1" <?php if(!empty($homepage_data->slider_enable)) echo 'selected'; ?>>Aktif</option>
					<option value="0" <?php if(empty($homepage_data->slider_enable)) echo 'selected'; ?>>Tidak Aktif</option>
				</select>
			</div>
		</div>
		<div id="slider_items" class="row media-manager">
			<?php $imgs = explode(",",$homepage_data->slider_list); $imgs_max = count($imgs); if(is_array($imgs) && $imgs_max){ $i=0; foreach($imgs as $im){ ?>
			<div id="slider_items_<?=$i?>" class="col-xs-6 col-sm-4 col-md-4 document galeri_item_item">
				<div class="thmb">
					<div class="thmb-prev" style="background-image:url(<?=base_url('media/uploads/default.jpg')?>); min-width: 100px;min-height: 60px;">
						<img src="<?=base_url($im)?>" class="img-responsive" alt="" />
					</div>
					<input type="hidden" name="image[]" value="<?=$im?>" />
					<div class="input-group">
						<span class="input-group-btn">
							<button type="button" class="btn btn-danger btn-media-del" data-media-div="slider_items" data-id="<?=$i?>"><i class="fa fa-trash-o"></i></button>
						</span>
					</div>
				</div>
			</div>
			<?php $i++; }} ?>
		</div>
	</div>
	<!-- END Slider -->

	<!-- Slider -->
	<div class="block full">
		<div class="block-title">
			<div class="block-options pull-right">
				<button href="#" class="btn btn-alt btn-sm btn-default btn-media-tambah" data-toggle="tooltip" title="Pilih gambar" data-original-title="Pilih gambar" data-media-div="block1_items" data-media-url="1" data-media-caption="1"><i class="fa fa-plus"></i></button>
			</div>
			<h2><strong>Kategori Block</strong></h2>
		</div>
		<div class="row" style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px #ededed solid;">
			<div class="col-md-6">&nbsp;</div>
			<div class="col-md-3">
				<select id="ihpblock1_layout" class="form-control">
					<option value="1" <?php if($homepage_data->block1_layout == '1') echo 'selected'; ?>>Layout A</option>
					<option value="0" <?php if($homepage_data->block1_layout == '0') echo 'selected'; ?>>Layout B</option>
				</select>
			</div>
			<div class="col-md-3">
				<select id="ihpblock1_enable" class="form-control">
					<option value="1" <?php if(!empty($homepage_data->block1_enable)) echo 'selected'; ?>>Aktif</option>
					<option value="0" <?php if(empty($homepage_data->block1_enable)) echo 'selected'; ?>>Tidak Aktif</option>
				</select>
			</div>
		</div>
		<div id="block1_items" class="row media-manager">
			<?php $imgs = explode(",",$homepage_data->block1_list); $imgs_max = count($imgs); if(is_array($imgs) && $imgs_max){ $i=0; foreach($imgs as $im){ ?>
			<div id="block1_items_<?=$i?>" class="col-xs-6 col-sm-4 col-md-4 document galeri_item_item">
				<div class="thmb">
					<div class="thmb-prev" style="background-image:url(<?=base_url('media/uploads/default.jpg')?>); min-width: 100px;min-height: 60px;">
						<img src="<?=base_url($im)?>" class="img-responsive" alt="" />
					</div>
					<input type="hidden" name="image[]" value="<?=$im?>" />
					<div class="input-group">
						<span class="input-group-btn">
							<button type="button" class="btn btn-danger btn-media-del" data-media-div="block1_items" data-id="<?=$i?>"><i class="fa fa-trash-o"></i></button>
						</span>
					</div>
				</div>
			</div>
			<?php $i++; }} ?>
		</div>
	</div>
	<!-- END Slider -->

	<!-- Content -->
	<div class="block full">

		<div class="block-title">
			<div class="block-options pull-right">
				<button id="atambah" href="#" class="btn btn-alt btn-sm btn-default" data-toggle="tooltip" title="Pilih Target" data-original-title="Pilih Target"><i class="fa fa-plus"></i></button>
			</div>
			<h2><strong>Target</strong></h2>
		</div>

		<div class="table-responsive">
			<table id="drTable" class="table table-vcenter table-condensed table-bordered">
				<thead>
					<tr>
						<th class="col-md-1 text-center">ID</th>
						<th class="col-md-1" >Prioritas</th>
						<th>Target</th>
						<th>Promosi</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>

	</div>
	<!-- END Content -->
</div>
