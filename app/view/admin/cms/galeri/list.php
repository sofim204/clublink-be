<style>
#copasmediabutton, .thmb-prev {
	cursor: pointer;
}
	#shTable_wrapper > div.dt-buttons {
	background: transparent;
	}
	#shTable_wrapper > div.dt-buttons > a {
	background: transparent;
	border: none;
	border-radius: 0;
	display: inline-block;
	padding: 6px 12px;
	margin-bottom: 0;
	font-size: 14px;
	font-weight: 400;
	line-height: 1.42857143;
	text-align: center;
	white-space: nowrap;
	vertical-align: middle;
	cursor: pointer;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	background-image: none;
	border: 1px solid transparent;
	border-radius: 4px;
	background-color: #31b0d5;
	border-color: #269abc;
	color: #fff;
	}
	option[disabled] {
	background-color: #ececec;
	color: #b0b0b0;
	font-style: italic;
	}
	.modal { overflow: auto !important; }
</style>
<div class="pageheader">
	<div class="row row-stat">
		<div class="col-md-10">
			<div class="media">
				<div class="pageicon pull-left">
					<i class="fa fa-file-text"></i>
				</div>
				<div class="media-body">
					<ul class="breadcrumb">
						<li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
						<li>Galeri</li>
					</ul>
					<h4>Galeri</h4>
					<div class="pull-right">

					</div>
				</div>
			</div><!-- media -->
		</div><!-- col-md-10 -->
		<div class="col-md-2">
			<a id="atambahbaru" href="<?php echo base_url_admin('cms/galeri/add/'); ?>" class="btn btn-info btn-block btn-create-msg"><i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;Tambah</a>
		</div><!-- col-md-2 -->
	</div><!-- row -->
</div><!-- pageheader -->

<div class="contentpanel">

	<div class="panel panel-secondary-head">
		<div class="panel-heading " style="padding: 0 20px; padding-top: 16px; display:none;">
			<div class="row">
				<div class="col-md-2">
					<div class="input-group mb15">
						<span class="input-group-addon"><i class="fa fa-book"></i></span>
						<select id="pagesize" name="pagesize" class="form-control">
							<option value="10">10</option>
							<option value="25">50</option>
							<option value="50">100</option>
							<option value="200">200</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-2">
					<div class="input-group mb15">
						<span class="input-group-addon"><i class="fa fa-file-text"></i></span>
						<select id="suppplier_selector" name="supplier_selector" class="form-control">
							<option value="">Semua Brand</option>
							<?php if(isset($author)){ foreach($author as $s){ ?>
								<option value="<?php echo $s->id; ?>"><?php echo $s->fnama; ?></option>
							<?php } } ?>
						</select>
					</div>
				</div>
				<div class="col-md-2">
					<div class="input-group mb15">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						<input id="min" type="text" placeholder="Dari Tanggal" class="form-control datepicker" />
					</div>
				</div>
				<div class="col-md-2">
					<div class="input-group mb15">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						<input id="max" type="text" placeholder="Ke Tanggal" class="form-control datepicker" />
					</div>
				</div>
				<div class="col-md-1">
					<a id="a_filter_data" href="#" class="btn btn-block btn-info"><i class="fa fa-filter"></i></a>
				</div>
			</div>
		</div>
		<!-- panel-heading -->
		<table id="shTable" class="table table-striped table-bordered responsive">
			<thead class="">
				<tr>
					<th>No</th>
					<th>Judul</th>
					<th>Kategori</th>
					<th>Gambar cover</th>
					<th>Tanggal</th>
					<th>Action</th>
				</tr>
			</thead>

			<tbody>
			</tbody>
		</table>
	</div><!-- panel -->

</div><!-- contentpanel -->

<div id="mpengumumanadd" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
				<h4 class="modal-title">Buat Blog Baru</h4>
			</div>
			<div id="modalBody" class="modal-body">
				<p id="ploading">Loading...</p>
				<form id="fpengumumanadd" method="post" action="<?php echo base_url("api/blog/add/"); ?>" style="display:none;"  class="form-horizontal">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<div class="col-md-12">
									<label for="ititle" class="control-label">Judul</label>
									<input type="text" id="ititle" name="title" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12">
									<label for="islug" class="control-label">Slug</label>
									<div class="input-group mb15">
										<span class="input-group-addon"><?php echo base_url("cerita/"); ?></span>
										<input type="text" id="islug" name="slug" class="form-control" />
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12">
									<label for="iexcerpt" class="control-label">Excerpt</label>
									<input type="text" id="iexcerpt" name="excerpt" class="form-control" />
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="well" style="min-height: 250px; margin-bottom: 0;">
								<img id="fiimage" src="" style="max-width: 250px; max-height: 200px;" />
								<input id="ifeatured_image" type="hidden" name="featured_image" />
							</div>
							<a id="aiimgsel" href="#" class="btn btn-info btn-block"><i class="fa fa-file-picture-o"></i> Pilih</a>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12">
							<label for="tacontent" class="control-label">Isi</label>
							<textarea id="tacontent" name="content" class="form-control mswrd" rows="15"></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-4">
							<label for="sstatus" class="">Status</label>
							<select id="sstatus" name="status" class="control-form select2">
								<option value="draft">Draft</option>
								<option value="publish">Publish</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<a id="apengumumansimpan" href="#" class="btn btn-success">Simpan Perubahan</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div id="list_modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
				<h4 class="modal-title">Ubah Tulisan Blog</h4>
			</div>
			<div id="modalBody" class="modal-body">
				<!-- form ubah -->
				<form id="fpengumumanedit" method="post" style="display:none;" action="<?php echo base_url("api/blog/update/"); ?>" class="form-horizontal">
					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<div class="col-md-12">
									<label for="ietitle" class="control-label">Judul</label>
									<input type="text" id="ietitle" name="title" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12">
									<label for="ieslug" class="control-label">Slug</label>
									<div class="input-group mb15">
										<span class="input-group-addon"><?php echo base_url("blog/detail/"); ?></span>
										<input type="text" id="ieslug" name="slug" class="form-control" />
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-12">
									<label for="ieexcerpt" class="control-label">Excerpt</label>
									<input type="text" id="ieexcerpt" name="excerpt" class="form-control" />
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="well" style="min-height: 250px; margin-bottom: 0;">
								<img id="fieimage" src="" style="max-width: 250px; max-height: 200px;" />
								<input id="iefeatured_image" type="hidden" name="featured_image" />
							</div>
							<a id="aieimgsel" href="#" class="btn btn-info btn-block"><i class="fa fa-file-picture-o"></i> Pilih</a>
						</div>

					</div>
					<div class="form-group">
						<div class="col-md-12">
							<label for="taecontent" class="control-label">Isi</label>
							<textarea id="taecontent" name="content" class="form-control mswrd" rows="15"></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6">
							<label for="sestatus" class="control-label">Isi</label>
							<select id="sestatus" name="status" class="form-control select2">
								<option value="draft">Draft</option>
								<option value="publish">Publish</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<p id="ploadingx">Loading...</p>
							<a id="aepreview" href="#" class="btn btn-primary" target="_blank">Preview Tulisan</a>
							<a id="apengumumanubah" href="#" class="btn btn-success">Simpan Perubahan</a>
							<a id="apengumumanhapus" href="#" class="btn btn-warning">Hapus</a>
						</div>
					</div>
				</form>
				<!-- form ubah -->
			</div><!-- modal diego -->
		</div><!-- modal content -->
	</div><!-- modal dialog -->
</div>

<div id="modal_media" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
				<h4 class="modal-title">Pilih Media untuk featured image</h4>
			</div>
			<div id="modalBody" class="modal-body">

				<div class="row">
					<div class="col-sm-9">
						<div id="rwm" class="row media-manager">
							<div class="col-md-12">
								<h2>Loading....</h2>
							</div>
						</div>
						<br>
						<ul class="pagination pagination-split mt5" style="display:none;">
							<li class="disabled"><a href="#"><i class="fa fa-angle-left"></i></a></li>
							<li><a href="#">1</a></li>
							<li class="active"><a href="#">2</a></li>
							<li><a href="#">3</a></li>
							<li><a href="#">4</a></li>
							<li><a href="#">5</a></li>
							<li><a href="#"><i class="fa fa-angle-right"></i></a></li>
						</ul>


					</div><!-- col-sm-9 -->
					<div class="col-sm-3">
						<div class="media-manager-sidebar">

							<button id="buploadshow" class="btn btn-primary btn-block">Upload Files</button>

							<div class="mb30"></div>

							<h5 class="lg-title">Folders <a href="" class="pull-right">+ Add New Folder</a></h5>
							<ul id="folder_list" class="folder-list">
								<li><a href="#" data-folder="/" class="folder_selector"><i class="fa fa-folder-o"></i> /</a></li>
							</ul>

						</div>
					</div><!-- col-sm-3 -->
				</div>

			</div><!-- modal diego -->
		</div><!-- modal content -->
	</div><!-- modal dialog -->
</div>


<div id="mfadd" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
				<h4 class="modal-title">Upload Media</h4>
			</div>
			<div id="modalBody" class="modal-body">
				<p id="mfaddloading">Loading...</p>
				<form id="mfaddform" method="post" action="<?php echo base_url("api/media/add/"); ?>" style="display:none;"  class="form-horizontal">

					<div class="form-group">
						<div class="col-md-12">
							<label for="ifolder" class="control-label">Pilih Folder</label>
							<div class="input-group mb15">
								<span class="input-group-addon">Folder</span>
								<select id="ifolder" name="folder" class="form-control select2">
									<option value="/">-- Root folder --</option>
								</select>
								<span id="ifoldertambah" class="input-group-addon">+ Tambah</span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12">
							<label for="ifile" class="control-label">Slug</label>
							<div class="input-group mb15">
								<span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
								<input id="ifile" name="file" class="form-control" type="file" accept="image/*" required />
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-sm-12">
							<button id="mfaddsimpan" type="submit" class="btn btn-success">Simpan</button>
						</div>
					</div>

				</form>
			</div>
		</div>
	</div>
</div>
