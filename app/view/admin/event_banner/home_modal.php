<!-- by Muhammad Sofi 8 January 2022 17:48 | create a preview video -->
<style>
	.thumbPreviewImage {
		border-radius: 20px;
		border-width: 1.5px;
		margin-left: 200px;
	}

	.textonly_video {
		display: none;
		margin-top: 70px;
		margin-left: 100px;
		width: 150px; 
  		border: 1px dashed #000000;
		text-align: center;
	}
	#icdate, #iedate {
		background-color: #FFFFFF;
	}

	/* increase font size sweetalert popup */
	.swal2-popup {
		font-size: 1.2rem !important;
		font-family: Georgia, serif;
	}

	.file-input {
		display: inline-block;
		text-align: left;
		background: #FAFAFA;
		padding: 16px;
		width: auto;
		position: relative;
		border-radius: 3px;
	}

	.file-input > [type='file'] {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		opacity: 0;
		z-index: 10;
		cursor: pointer;
	}

	.file-input > .button {
		display: inline-block;
		cursor: pointer;
		background: #EAEAEA;
		padding: 8px 16px;
		border-radius: 2px;
		margin-right: 8px;
	}

	.file-input:hover > .button {
		background: #e67e22;
		color: #ffffff;
	}

	.file-input > .label {
		color: #000;
		white-space: nowrap;
		opacity: .7;
	}

	.file-input.-chosen > .label {
		opacity: 1;
	}

</style>

<!-- by Muhammad Sofi 5 January 2022 20:22 | can input file image/video, and add validation when upload file -->
<!-- modal options -->
<!-- <input id="video_id" type="hidden" /> -->
<div id="modal_options" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Options</strong></h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="aEditBanner" href="#" class="btn btn-primary text-center"><i class="fa fa-edit"></i> Edit</a>
					</div>
				</div>
				<div class="row" style="margin-bottom: 6px;"></div>
				<!-- by Muhammad Sofi 31 January 2022 14:14 | change thumbnail is not used anymore -->
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="aEditBannerThumbnail" href="#" class="btn btn-info text-center"><i class="fa fa-edit"></i> Change Thumbnail</a>
					</div>
				</div> 
				<div class="row" style="margin-bottom: 6px;"></div>
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="bhapus" href="javascript:void(0);" class="btn btn-danger text-center"><i class="fa fa-trash-o"></i> Delete</a>
					</div>
				</div>
				<div class="row" style="margin-bottom: 6px;"></div>
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="atestURL" href="javascript:void(0)" class="btn btn-default text-center" target="_blank" style="background-color: #B6B4B4; color:#FFFFFF;"> Detail Banner</a>
					</div>
				</div>
				<div class="row" style="margin-bottom: 6px;"></div>
			</div>
		</div>
	</div>
</div>

<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add Event Banner</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
					<!-- by Muhammad Sofi 3 January 2022 17:18 | add description for event banner -->
						<div class="form-group">
							<!-- by Muhammad Sofi 3 January 2022 18:23 | add title for event banner  -->
							<div class="col-md-12">
								<label class="control-label" for="ijudul">Title *</label>
								<input id="ijudul" type="text" name="judul" class="form-control" minlength="1" placeholder="Event Title" autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label class="" for="itype_event_banner">Type</label>
								<select id="itype_event_banner" name="type_event_banner" class="form-control" placeholder="--Choose--">
									<option value="" disabled selected>--Choose--</option>
									<option value="original">Original</option>
									<option value="shop">Shop</option>
									<option value="product">Product</option>
									<option value="community">Community Post</option>
									<option value="polling">Polling</option>
									<option value="activity_dashboard">Activity Dashboard</option>
									<option value="guide">SPT Guide</option>
									<option value="webview_wallet">Webview Wallet</option>
									<option value="invitation_page">Invitation Page</option>
									<option value="webview">Webview</option>
									<option value="pulsa">Pulsa</option>
									<option value="babyboom">Babyboom</option>
								</select>
							</div>
							<div class="col-md-4" id="row_url" style="display: none;">
								<label for="input_url_webview">Url</label>
								<input class="form-control" id="input_url_webview" type="text" name="url_webview" value=""/> <!-- &url_parameter= -->
							</div>
							<div class="col-md-4" id="row_seller_shop" style="display: none;">
								<label for="select_seller_shop">Seller Shop</label>
								<select id="select_seller_shop" class="form-control"></select>
								<input id="select_seller_shop_value" type="hidden" name="seller_id" />
							</div>
							<div class="col-md-4" id="row_seller_shop_product" style="display: none;">
								<label for="select_seller_shop_product">Seller Shop</label>
								<select id="select_seller_shop_product" class="form-control"></select>
								<input id="select_seller_shop_product_value" type="hidden" />
							</div>
							<div class="col-md-4" id="row_product_detail" style="display: none;">
								<label for="select_product_detail">Product Detail</label>
								<select id="select_product_detail" class="form-control"></select>
								<input id="select_product_detail_value" type="hidden" name="product_id" />
							</div>
							<div class="col-md-4" id="row_community" style="display: none;">
								<label for="select_community">Community Post</label>
								<select id="select_community" class="form-control"></select>
								<input id="select_community_value" type="hidden" name="community_id" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="isuploadfile">Upload File Type*</label>
								<select id="isuploadfile" class="form-control" name="url_type">
									<option value="">--Select--</option>
									<option value="image">Image</option>
									<option value="video">Video</option>
								</select>
							</div>
							<!-- by Muhammad Sofi 7 January 2022 21:19 | change position of input file beside of upload file type -->
							<div class="col-md-6" id="chooseFile" style="display: none;">
								<label class="" for="ifile">Event Banner Photo *</label>
								<!-- <input id="ifile" type="file" name="url" class="form-control" accept="image/png, image/jpg, image/jpeg" required />  --> <!-- by Muhammad Sofi 10 January 2022 19:46 | filter input file upload image only -->
								<!-- by Muhammad Sofi 17 January 2022 11:06 | change model to filter input file -->
								<!-- <input id="ifile" type="file" name="url" class="custom-file" style="margin:1px 0" accept=".jpg, .jpeg, .png, .gif" required /> -->
								<div class="file-input">
									<input type="file" id="ifile" name="url" accept=".jpg, .jpeg, .png, .gif" required />
									<span class="button">Choose</span>
									<span class="label" data-js-label>No file selected</label>
								</div>
							</div>
							<div class="col-md-6" id="chooseFileVideo" style="display: none;">
								<label class="" for="ifileVideo">Event Banner Video *</label>
								<!-- <input id="ifileVideo" type="file" name="url" class="form-control" accept="video/3gp, video/mp4, video/x-matroska, video/quicktime" required /> --> <!-- by Muhammad Sofi 10 January 2022 19:46 | filter input file video -->
								<!-- by Muhammad Sofi 17 January 2022 11:06 | change model to filter input file -->
								<!-- <input id="ifileVideo" type="file" name="url" class="custom-file" style="margin:1px 0" accept=".mp4, .mkv, .mov, .3gp" required /> by Muhammad Sofi 20 January 2022 11:55 | request from mr jackie, add additional ext for video -->
								<div class="file-input">
									<input type="file" id="ifileVideo" name="url" accept=".mp4, .mkv, .mov, .3gp" required /> <!-- by Muhammad Sofi 20 January 2022 11:55 | request from mr jackie, add additional ext for video -->
									<span class="button">Choose</span>
									<span class="label" data-js-label>No file selected</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<center><img id="original-File" src="" class="img-responsive" alt="" style="display: none;"></center>
							</div>
							<div class="col-md-6">
								<center><img id="upload-Preview" src="" class="img-responsive" alt="" style="width: 320px;"></center>
							</div>
							<!-- by Muhammad Sofi 8 January 2022 17:48 | create a preview video -->
							<div class="col-md-6">
								<label for="" id="textvideopreview" style="display: none;">Video Preview</label>
								<div id="textonly_video" class="textonly_video">Video Preview will show after upload file</div>
								<video id="video-Preview" width="320px" height="240px" controls style="display: none; margin-top:-20px;">
									<source src="" type="video/mp4">
									<source src="" type="video/mkv">
									<source src="" type="video/mov">
									<source src="" type="video/3gp">
								</video>
							</div>
							<div class="col-md-6" id="divPreview" style="display: none;">
								<div class="input-group">
									<label for="">Image Thumbnail</label>
									<canvas id="myCanvas" style="border:3px solid #d3d3d3; max-width:320px; margin-top:12px;">
								</div>
								<div class="input-group">
									<button type="button" id="snap" class="btn btn-sm btn-primary">Take snapshot</button>
								</div>
								<input type="hidden" id="textimagebase64" name="img_thumbnail" autocomplete="off" />
							</div>
						</div>
						<!-- by Muhammad Sofi 10 January 2022 18:53 | change position input description, hide if input event banner video -->
						<div class="form-group" id="input_description" style="display: none;">
							<div class="col-md-12">
								<label class="control-label" for="iteks">Description</label>
								<div id="editor">
									<textarea id="iteks" name="teks" class="form-control" rows="5"></textarea>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label class="" for="ipriority">Priority (1-100)</label>
								<select id="ipriority" name="priority" class="form-control" required>
									<?php for ($i=1; $i <= 100; $i++) { ?>	
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>
								</select>
							</div>
							<div class="col-md-3">
								<label class="" for="icdate">Start Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="icdate" type="text" name="start_date" class="form-control input-datepicker add_date" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" readonly="readonly" />
								</div>
							</div>
							<div class="col-md-3">
								<label class="" for="iedate">End Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="iedate" type="text" name="end_date" class="form-control input-datepicker add_date" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" readonly="readonly" />
								</div>
							</div>
							<div class="col-md-3">
								<label class="" for="ieisactive">Status</label>
								<select id="ieisactive" name="is_active" class="form-control" required>
									<option value="0">Not Active</option>
									<option value="1">Active</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>
<!-- modal edit -->
<div id="modal_edit" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit Event Banner</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftedit" method="post" enctype="multipart/form-data"  class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<!-- by Muhammad Sofi 3 January 2022 17:18 | add description for event banner -->
						<div class="form-group">
							<!-- by Muhammad Sofi 3 January 2022 18:23 | add title for event banner  -->
							<div class="col-md-12">
								<label class="control-label" for="iejudul">Title *</label>
								<input id="iejudul" type="text" name="judul" class="form-control" minlength="1" placeholder="Event Title" autocomplete="off" />
							</div>
							<!-- by Muhammad Sofi 17 January 2022 11:06 | change the old model of edit event banner -->
							<div class="col-md-12" id="textarea_editbanner" style="display:none;">
								<label class="control-label" for="ieteks">Description</label>
								<div id="editor1">
									<textarea id="ieteks" name="teks" class="form-control" rows="5"></textarea>
								</div>
							</div>
						</div>
						<!-- START by Muhammad Sofi 23 January 2022 23:00 | improvement and bug fixing on edit event banner -->
						<div class="form-group">
							<div class="col-md-6" id="editFilePhoto" style="display: none;">
								<label class="" for="iefile">Event Banner Photo</label>
								<!-- <input id="iefile" type="file" name="url" class="custom-file" style="margin:1px 0" placeholder="Picture" placeholder="Event Photo" accept=".jpg, .jpeg, .png, .gif" /> -->
								<div class="file-input">
									<input type="file" id="iefile" name="url" placeholder="Picture" placeholder="Event Photo" accept=".jpg, .jpeg, .png, .gif" />
									<span class="button">Choose</span>
									<span class="label" data-js-label>No file selected</label>
								</div>
							</div>
							<div class="col-md-6" id="editFileVideo" style="display: none;">
								<label class="" for="iefile">Event Banner Video</label>
								<!-- <input id="iefileVideo" type="file" name="url" class="custom-file" style="margin:1px 0" placeholder="Video" accept=".mp4, .mkv, .mov, .3gp" /> -->
								<div class="file-input">
									<input type="file" id="iefileVideo" name="url" placeholder="Video" placeholder="Event Photo" accept=".mp4, .mkv, .mov, .3gp" />
									<span class="button">Choose</span>
									<span class="label" data-js-label>No file selected</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<center><img id="imageDisplay" src="" class="img-responsive" width="200px" height="100px" style="border: 1px solid #000000" alt=""></center>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<!-- <label for="" id="textvideopreview" style="display: none;">Video Preview</label>
								<div id="textonly_video" class="textonly_video">Video Preview will show after upload file</div> -->
								<div class="input-group">
									<label for="" id="textvideopreviewedit" style="display: none;">Video Preview</label>
								</div>
								<video id="videopreviewedit" width="320px" height="240px" controls style="display: none; margin-top:-20px;">
									<source src="" type="video/mp4">
									<source src="" type="video/mkv">
									<source src="" type="video/mov">
									<source src="" type="video/3gp">
								</video>
							</div>
							<div class="col-md-6" id="divPreviewEdit" style="display: none;">
								<div class="input-group">
									<label for="">Image Thumbnail</label>
									<canvas id="myCanvasEdit" style="border:3px solid #d3d3d3; max-width:320px; margin-top:12px;">
								</div>
								<div class="input-group">
									<button type="button" id="snap_edit" class="btn btn-sm btn-primary">Take snapshot</button>
								</div>
								<input type="hidden" id="textimagebase64edit" name="img_thumbnail" autocomplete="off" />
								<!-- <div style="text-align: center;"><img id="fotone" src="" class="img-responsive" alt=""></div> -->
							</div>
							<div class="col-md-6">&nbsp;</div>
						</div>
						<!-- END by Muhammad Sofi 23 January 2022 23:00 | improvement and bug fixing on edit event banner -->
						<!-- current seller shop or product detail -->
						<div class="form-group">
							<div class="col-md-4">
								<label for="ietype_value_edit">Event Banner Type</label>
								<input id="ietype_value_edit" type="text" style="background-color:#fafafa" maxlength="10" size="10" readonly />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label for="ieselect_seller_shop_value_edit">Seller Shop ID</label>
								<input id="ieselect_seller_shop_value_edit" type="text" style="background-color:#fafafa" name="seller_id" maxlength="10" size="10" readonly />
							</div>
							<div class="col-md-4">
								<label for="ieselect_product_detail_value_edit">Product Detail ID</label>
								<input id="ieselect_product_detail_value_edit" type="text" style="background-color:#fafafa" name="product_id" maxlength="10" size="10" readonly />
							</div>
							<div class="col-md-4">
								<label for="ieselect_community_value_edit">Community ID</label>
								<input id="ieselect_community_value_edit" type="text" style="background-color:#fafafa" name="community_id" maxlength="10" size="10" readonly />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label class="" for="itype_event_banner_edit">Change Type</label>
								<select id="itype_event_banner_edit" name="type_event_banner" class="form-control">
									<option value="" disabled selected>--Choose--</option>
									<option value="original">Original</option>
									<option value="shop">Shop</option>
									<option value="product">Product</option>
									<option value="community">Community Post</option>
									<option value="polling">Polling</option>
								</select>
							</div>
							<div class="col-md-4" id="row_seller_shop_edit" style="display: none;">
								<label for="select_seller_shop_edit">Seller Shop</label>
								<select id="select_seller_shop_edit" class="form-control"></select>
								<input id="select_seller_shop_value_edit" type="text" style="background-color:#fafafa" readonly />
							</div>
							<div class="col-md-4" id="row_seller_shop_product_edit" style="display: none;">
								<label for="select_seller_shop_product_edit">Seller Shop</label>
								<select id="select_seller_shop_product_edit" class="form-control"></select>
								<input id="select_seller_shop_product_value_edit" type="text" style="background-color:#fafafa" readonly />
							</div>
							<div class="col-md-4" id="row_product_detail_edit" style="display: none;">
								<label for="select_product_detail_edit">Product Detail</label>
								<select id="select_product_detail_edit" class="form-control"></select>
								<input id="select_product_detail_value_edit" type="text" style="background-color:#fafafa" readonly />
							</div>
							<div class="col-md-4" id="row_community_edit" style="display: none;">
								<label for="select_community_edit">Community Post</label>
								<select id="select_community_edit" class="form-control"></select>
								<input id="select_community_value_edit" type="text" style="background-color:#fafafa" readonly />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="iecdate">Start Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="iecdate" type="text" name="start_date" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" />
								</div>
							</div>
							<div class="col-md-6">
								<label class="" for="ieedate">End Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="ieedate" type="text" name="end_date" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="iepriority">Priority (1-100)</label>
								<select id="iepriority" name="priority" class="form-control" required>
									<!-- by Muhammad Sofi 14 January 2022 9:47 | add 1-100 number to priority -->
									<?php for ($i=1; $i <= 100; $i++) { ?>	
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>
								</select>
							</div>
							<div class="col-md-6">
								<label class="" for="ieis_active">Status</label>
								<select id="ieis_active" name="is_active" class="form-control" required>
									<option value="1">Active</option>
									<option value="0">Not Active</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
              				<!-- <button id="bhapus" type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Delete</button> -->
							<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- modal edit thumbnail -->
<div id="modal_edit_thumbnail" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Change Thumbnail</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fthumbnail_change" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="ieimage_icon">Choose Thumbnail File</label>  
								<!-- <input id="ieimage_icon" type="file" name="img_thumbnail" class="form-control" accept="image/x-png,image/jpeg,image/jpg" required /> -->
								<!-- by Muhammad Sofi 17 January 2022 11:06 | change model to filter input file -->
								<!-- <input id="ieimage_icon" type="file" name="img_thumbnail" class="custom-file" accept=".jpg, .jpeg, .png, .gif" required /> -->
								<div class="file-input">
									<input type="file" id="ieimage_icon" name="img_thumbnail" accept=".jpg, .jpeg, .png, .gif" required />
									<span class="button">Choose</span>
									<span class="label" data-js-label>No file selected</label>
								</div>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-upload"></i> Upload</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>