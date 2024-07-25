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
				<!-- <div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="bhapus" href="javascript:void(0);" class="btn btn-danger text-center"><i class="fa fa-trash-o"></i> Delete</a>
					</div>
				</div>
				<div class="row" style="margin-bottom: 6px;"></div> -->
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="atestURL" href="javascript:void(0)" class="btn btn-default text-center" target="_blank" style="background-color: #B6B4B4; color:#FFFFFF;"> Detail Ads</a>
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
				<h2 class="modal-title"><strong>Add Advertisement</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-6" id="chooseFile">
								<label class="" for="ifile">Advertisement Photo *</label>
								<div class="file-input">
									<input type="file" id="ifile" name="url" accept=".jpg, .jpeg, .png, .gif" required />
									<span class="button">Choose</span>
									<span class="label" data-js-label>No file selected</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<img id="upload-Preview" src="" class="img-responsive" alt="" style="width: 320px;">
							</div>
							<div class="col-md-6">
								<div style="text-align: center;">
									<img id="original-File" src="" class="img-responsive" alt="" style="display: none;">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4">
								<label class="" for="iitype_ads">Type</label>
								<select id="iitype_ads" name="type_ads" class="form-control" required>
									<option value="original" selected>original</option>
									<option value="shop">shop</option>
									<option value="product">product</option>
									<option value="community">community</option>
									<option value="polling">polling</option>
									<option value="activity_dashboard">activity_dashboard</option>
									<option value="guide">guide</option>
									<option value="webview_wallet">webview_wallet</option>
									<option value="invitation_page">invitation_page</option>
									<option value="webview">Webview</option>
									<option value="webview_wallet_ads">Webview Wallet Ads</option>
								</select>
							</div>
							<div class="col-md-6" id="webview_teks" style="display: none;">
								<label for="input_url_webview">URL</label>
								<input class="form-control" id="input_url_webview" type="text" name="url_webview" value="" /> 
							</div>
						</div>
						<fieldset>
							<div class="form-group" id="banner_ads_detail" style="display: none;">
								<!-- by Muhammad Sofi 3 January 2022 18:23 | add title for event banner  -->
								<div class="col-md-12">
									<label class="control-label" for="iejudul">Title *</label>
									<input id="iejudul" type="text" name="judul" class="form-control" minlength="1" placeholder="Event Title" autocomplete="off" required />
								</div>
								<!-- by Muhammad Sofi 17 January 2022 11:06 | change the old model of edit event banner -->
								<div class="col-md-12" id="textarea_editbanner">
									<label class="control-label" for="ieteks">Description</label>
									<div id="editor1">
										<textarea id="ieteks" name="teks" class="form-control" rows="5"></textarea>
									</div>
								</div>
							</div>
						</fieldset>
						<div class="form-group">
							<div class="col-md-2">
								<label class="" for="ipriority">Priority (1-100)</label>
								<select id="ipriority" name="priority" class="form-control" required>
									<?php for ($i=1; $i <= 100; $i++) { ?>	
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>
								</select>
							</div>
							<div class="col-md-3">
								<label class="" for="iis_active">Status</label>
								<select id="iis_active" name="is_active" class="form-control" required>
									<option value="0">Inactive</option>
									<option value="1">Active</option>
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
				<h2 class="modal-title"><strong>Edit Advertisement</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftedit" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-6" id="editFilePhoto" style="display: none;">
								<label class="" for="iefile">Advertisement Photo</label>
								<div class="file-input">
									<input type="file" id="iefile" name="url" placeholder="Picture" placeholder="Event Photo" accept=".jpg, .jpeg, .png, .gif" />
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
							<div class="col-md-4">
								<label class="" for="ieitype_ads">Type</label>
								<select id="ieitype_ads" name="type_ads" class="form-control" required>
									<option value="original">original</option>
									<option value="shop">shop</option>
									<option value="product">product</option>
									<option value="community">community</option>
									<option value="polling">polling</option>
									<option value="activity_dashboard">activity_dashboard</option>
									<option value="guide">guide</option>
									<option value="webview_wallet">webview_wallet</option>
									<option value="invitation_page">invitation_page</option>
									<option value="webview">Webview</option>
									<option value="webview_wallet_ads">webview_wallet_ads</option>
								</select>
							</div>
							<div class="col-md-8" id="edit_webview_teks">
								<label for="edit_url_webview">URL</label>
								<input class="form-control" id="edit_url_webview" type="text" name="url_webview" />
							</div>
						</div>
						<fieldset>
							<div class="form-group" id="edit_banner_ads_detail">
								<!-- by Muhammad Sofi 3 January 2022 18:23 | add title for event banner  -->
								<div class="col-md-12">
									<label class="control-label" for="ieejudul">Title *</label>
									<input id="ieejudul" type="text" name="judul" class="form-control" minlength="1" placeholder="Event Title" autocomplete="off" required />
								</div>
								<!-- by Muhammad Sofi 17 January 2022 11:06 | change the old model of edit event banner -->
								<div class="col-md-12" id="textarea_editbanner">
									<label class="control-label" for="ieeteks">Description</label>
									<div id="editor1">
										<textarea id="ieeteks" name="teks" class="form-control" rows="5"></textarea>
									</div>
								</div>
							</div>
						</fieldset>
						<div class="form-group">
							<div class="col-md-2">
								<label class="" for="iepriority">Priority (1-100)</label>
								<select id="iepriority" name="priority" class="form-control" required>
									<!-- by Muhammad Sofi 14 January 2022 9:47 | add 1-100 number to priority -->
									<?php for ($i=1; $i <= 100; $i++) { ?>	
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>
								</select>
							</div>
							<div class="col-md-3">
								<label class="" for="ieis_active">Status</label>
								<select id="ieis_active" name="is_active" class="form-control" required>
									<option value="1">Active</option>
									<option value="0">Not Active</option>
								</select>
							</div>
							<div class="col-md-3">
								<label class="" for="iecdate">Start Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="iecdate" type="text" name="start_date" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" />
								</div>
							</div>
							<div class="col-md-3">
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