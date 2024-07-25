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
            <li><a href="<?php echo base_url_admin(); ?>"><i class="glyphicon glyphicon-home"></i></a></li>
            <li><a href="<?php echo base_url_admin('cms/galeri'); ?>">Galeri</a></li>
            <li>Edit</li>
          </ul>
          <h4>Edit</h4>
          <div class="pull-right">
            </div>
        </div>
      </div><!-- media -->
    </div><!-- col-md-10 -->
    <div class="col-md-2">
      <div class="btn-group">
        <a id="aeditback" href="<?php echo base_url_admin('cms/galeri'); ?>" class="btn btn-info "><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;&nbsp;Kembali</a>
        <a id="afeditsubmit" href="#" class="btn btn-success"><i class="fa fa-save"></i></a>
      </div>
    </div><!-- col-md-2 -->
  </div><!-- row -->
</div><!-- pageheader -->
<div class="contentpanel">
  <div class="panel panel-secondary-head">

    <form id="fedit" method="post" action="<?php echo base_url("api/galeri/update/"); ?>" class="form-horizontal">

      <div class="form-group">
        <div class="col-md-12">
          <label for="ietitle" class="control-label">Judul</label>
          <input type="text" id="ietitle" name="title" class="form-control" value="<?php $this->__e($blog->title); ?>" />
        </div>
        <div class="col-md-12">
          <label for="iekategori" class="control-label">Kategori</label>
          <input type="text" id="iekategori" name="kategori" class="form-control" value="<?php $this->__e($blog->kategori); ?>" />
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-12">
          <label for="taecontent" class="control-label">Isi</label>
          <textarea id="taecontent" name="content" class="form-control mswrd" rows="15"><?php $this->__e($blog->content,'blog'); ?></textarea>
        </div>
      </div>
      <div class="form-group " style=" padding:0.5em;">
        <div class="col-md-12">
          <div class="row" style="border: 1px #c0c0c0 solid;">

            <div class="col-md-12" style="background-color: rgba(0,0,0,0.2)">
              <div class="row">
                <div class="col-md-10">
                  <h4>Galeri Item</h4>
                </div>
                <div class="col-md-2" style="padding-top: 0.25em; padding-bottom: 0.25em;">
          				<button id="bgaleritambah" class="btn btn-primary btn-block">+ Galeri</button>
                </div>
              </div>

            </div>
            <div class="col-md-12" style="min-height: 30vh; padding-top: 0.5em;">
              <div id="dgaleri_items" class="row media-manager">

              <?php $i=0; foreach($blog->items as $item){ ?>
                <div id="galeri_item_<?php echo $i; ?>" class="col-xs-6 col-sm-4 col-md-4 document galeri_item_item">
                  <div class="thmb">
                    <div class="thmb-prev" style="background-image:url('<?php echo base_url('media/blog/default.jpg'); ?>'); min-width: 100px;min-height: 60px;">
                      <img src="<?php echo base_url($item->image); ?>" class="img-responsive" alt="">
                    </div>
                    <input type="hidden" name="image[]" value="<?php echo $item->image; ?>" />
                    <div class="input-group">
                      <input type="text" id="galeri_item_caption_<?php echo $i; ?>" name="caption[]" value="<?php echo $item->caption; ?>" class="form-control " placeholder="Caption" />
                      <span class="input-group-btn">
                        <button id="bgaleri_item_del" type="button" class="btn btn-danger" data-id="<?php echo $i; ?>"><i class="fa fa-trash-o"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
              <?php } ?>

              </div>
      			</div>
          </div>

        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-12">
          <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>




<!-- media stuff-->
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
