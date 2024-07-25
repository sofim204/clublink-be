api_admin/cms/blog/
<div id="mpengumumanadd" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
				<h4 class="modal-title">Buat Blog Baru</h4>
			</div>
			<div id="modalBody" class="modal-body">
				<p id="ploading">Loading...</p>
				<form id="fpengumumanadd" method="post" action="<?php echo base_url("api_admin/cms/blog/add/"); ?>" style="display:none;"  class="form-horizontal">
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
										<span class="input-group-addon"><?php echo base_url("blog/"); ?></span>
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
				<form id="fpengumumanedit" method="post" style="display:none;" action="<?php echo base_url("api_admin/cms/blog/update/"); ?>" class="form-horizontal">
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
										<span class="input-group-addon"><?php echo base_url("blog/"); ?></span>
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
