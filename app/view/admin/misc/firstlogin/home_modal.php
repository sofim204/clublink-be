<style>
	#iedate, #iecdate {
		background-color: #FFFFFF;
	}
</style>

<!-- modal options -->
<div id="modal_options" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Options</strong></h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="aEditImage" href="#" class="btn btn-primary text-center"><i class="fa fa-edit"></i> Edit</a>
					</div>
				</div>
				<div class="row" style="margin-bottom: 6px;"></div> 
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="bhapus_modal" href="javascript:void(0);" class="btn btn-danger text-center"><i class="fa fa-trash-o"></i> Delete</a>
					</div>
				</div>
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
				<h2 class="modal-title"><strong>Add Image</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah"  method="post" enctype="multipart/form-data"  class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
                                <div class="center-image"><img id="upload-Preview" src="" class="img-responsive" alt="">
                                    <span id="placeholder_image"><strong>Image will show after upload</strong></span>
                                </div>
							</div>
						</div>
                        <div style="border-bottom: 1px dashed #635F5F; margin: 0 5px;"></div>
						<div class="form-group">
							<div class="col-md-4">
								<label class="" for="ifile">Image</label>
								<input id="ifile" type="file" name="url" class="form-control" placeholder="Image" accept=".jpg, .jpeg, .png, .gif" required />
							</div>
							<div class="col-md-4">
                                <label class="" for="ipriority">Priority (1-100)</label>
								<select id="ipriority" name="priority" class="form-control" required>
									<?php for ($i=1; $i <= 100; $i++) { ?>	
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>
								</select>
							</div>
                            <div class="col-md-4">
                                <label class="" for="iis_active">Status</label>
								<select id="iis_active" name="is_active" class="form-control" required>
									<option value="1">Active</option>
									<option value="0">Not Active</option>
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
				<h2 class="modal-title"><strong>Edit Image</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftedit"  method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
                                <div class="center-image"><img id="imageDisplay" src="" class="img-responsive" width="300px" alt="firstlogin_edit"></div>
							</div>
						</div>
                        <div style="border-bottom: 1px dashed #635F5F; margin: 0 5px;"></div>
						<div class="form-group">
							<div class="col-md-4">
								<label class="" for="iefile">Image</label>
								<input id="iefile" type="file" name="url" class="form-control" placeholder="Picture" accept=".jpg, .jpeg, .png, .gif" />
							</div>
                            <div class="col-md-4">
								<label class="" for="iepriority">Priority (1-100)</label>
								<select id="iepriority" name="priority" class="form-control" required>
									<?php for ($i=1; $i <= 100; $i++) { ?>	
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>
								</select>
							</div>
							<div class="col-md-4">
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
              				<button id="bhapus" type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Delete</button>
							<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>