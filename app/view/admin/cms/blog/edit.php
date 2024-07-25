<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
					<a id="abackx" href="<?php echo base_url_admin('cms/blog/'); ?>" class="btn btn-default"><i class="fa fa-chevron-left"></i> Kembali</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<button id="bgaleritambah" class="btn btn-default" data-toggle="tooltip" title="Pilih gambar" data-original-title="Pilih gambar"><i class="fa fa-plus"></i> Pilih Gambar</button>
					<button id="bsubmit2" class="btn btn-primary" ><i class="fa fa-save"></i> Simpan Perubahan</button>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>CMS</li>
		<li>Blog</li>
		<li>Edit</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="fedit" action="<?php echo base_url_admin('cms/blog/edit/'); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>Data Utama</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="iekategori">Kategori</label>
					<div class="input-group">
            <select id="iekategori" name="kategori" class="form-control">
							<option value="Uncategorized">Uncategorized</option>
							<?php if(isset($kategori)){ foreach($kategori as $k){ ?>
							<option value="<?=$k->kategori?>"><?=$k->kategori?></option>
							<?php }} ?>
						</select>
            <div class="input-group-btn">
              <button id="iekategori_tambah" type="button" class="btn btn-primary dropdown-toggle"><i class="fa fa-plus"></i></button>
            </div>
	        </div>
				</div>
				<div class="col-md-9">
					<label class="" for="ietitle">Judul *</label>
					<input id="ietitle" type="text" name="title" class="form-control" minlength="1" placeholder="Judul Blog" autocomplete="off" required />
				</div>
			</div>
		</div>
		<div class="block full row">
			<div class="block-title">
				<div class="block-options pull-right">
					<a href="#" class="btn btn-alt btn-sm btn-default btn-hidden-block" >
						<i class="fa fa-minus"></i>
					</a>
				</div>
				<h2><strong>SEO</strong></h2>
			</div>
			<div class="form-group">
				<div class="row">
					<div id="featured_image_pilih" class="col-md-4" style="cursor:pointer; padding:0.25em; border: 1px #ededed solid; border-radius: 5px;">
						<img id="img_featured_image" src="<?=base_url()?>media/uploads/default.jpg" class="img-responsive" onerror="this.src='<?=base_url()?>media/uploads/default.jpg';" />
						<input id="iefeatured_image" name="featured_image" type="hidden" value="media/uploads/default.jpg" />
					</div>
					<div class="col-md-8">
						<div class="row">
							<div class="col-md-12">
								<label class="control-label" for="iemtitle">Meta Title</label>
								<input id="iemtitle" type="text" name="mtitle" class="form-control" minlength="1" maxlength="90" />
							</div>
							<div class="col-md-12">
								<label class="control-label" for="ieslug">SLUG*</label>
								<input id="ieslug" type="text" name="slug" class="form-control" minlength="1" placeholder="Slug" required />
							</div>
							<div class="col-md-12">
								<label class="control-label" for="iemkeyword">Meta Keyword</label>
								<input id="iemkeyword" type="text" name="mkeyword" class="form-control" minlength="1" placeholder="" />
							</div>
							<div class="col-md-12">
								<label class="control-label" for="iemdescription">Meta Description</label>
								<textarea id="iemdescription" name="mdescription" class="form-control" maxlength="160" rows="4"></textarea>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<div class="block-options pull-right">
					<a href="#" class="btn btn-alt btn-sm btn-default btn-hidden-block" >
						<i class="fa fa-minus"></i>
					</a>
				</div>
				<h2><strong>Isi</strong></h2>
			</div>
			<div class="form-group">
				<div class="form-group">
					<div class="col-md-12">
						<textarea id="iecontent" name="content" class="ckeditor" rows="5"></textarea>
					</div>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Pilihan</strong></h2>
			</div>
			<div class="form-group">
				<div class="form-group">
					<div class="col-md-12">
						<label class="control-label" for="ietags">Tags</label>
						<input type="text" id="ietags" name="tags" class="form-control" />
					</div>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Penting</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="iestatus">Status</label>
					<select id="iestatus" name="status" class="form-control">
						<option value="publish">Publish</option>
						<option value="draft">Draft</option>
					</select>
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
						<button type="submit" value="" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Perubahan</button>
					</div>
				</div>
			</div>
		</div>
	</form>
	<!-- END Content -->
</div>
