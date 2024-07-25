<!-- media-->
<!-- media modal -->
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
<!-- end media modal -->

<!-- media add modal -->
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
								<select id="ifolder" name="folder" class="form-control">
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
<!-- end media add modal -->
<!-- end media-->