<!-- modal options -->
<div id="modal_options" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h2 class="modal-title">Options</h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="editbanner" href="javascript:void(0);" class="btn btn-primary text-center"><i class="fa fa-edit"></i> Edit</a>
					</div>
				</div>
				<div class="row" style="margin-bottom: 6px;"></div>
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="deletebanner" href="javascript:void(0);" class="btn btn-danger text-center"><i class="fa fa-trash-o"></i> Delete</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h3 class="modal-title"><strong>Add New Banner Carousel</strong></h3>
			</div>
			<div class="modal-body">
				<form id="ftambah" method="post" enctype="multipart/form-data"  class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<div style="text-align: center;"><img id="upload-Preview" src="" class="img-responsive" alt=""></div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ifile">Banner Carousel Image (1474 x 544) px *</label>
								<input id="ifile" type="file" name="url" class="form-control" placeholder="Banner Carousel Image" accept=".jpg, .jpeg, .png" required />
							</div>
							<div class="col-md-6">
								<label for="itypelanguage">Language Type</label>
								<select id="itypelanguage" name="type_language" class="form-control" required>
									<option value="">--select--</option>
									<option value="indonesia">Indonesia</option>
									<option value="english">English</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ipriority">Priority (1-100)</label>
								<select id="ipriority" name="priority" class="form-control" required>
									<?php for ($i=1; $i <= 100; $i++) { ?>	
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-md-6">
								<label for="iis_active">Status</label>
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
		</div>
	</div>
</div>

<!-- modal edit -->
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Edit Banner Carousel</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftedit"  method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<div style="text-align: center;"><img id="imageDisplay" src="" class="img-responsive" alt=""></div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="iefile">Banner Carousel Image (1474 x 544) px *</label>
								<input id="iefile" type="file" name="url" class="form-control" placeholder="Image" accept=".jpg, .jpeg, .png" />
							</div>
							<div class="col-md-6">
								<label for="ietypelanguage">Language Type</label>
								<select id="ietypelanguage" name="type_language" class="form-control" required>
									<option value="">--select--</option>
									<option value="indonesia">Indonesia</option>
									<option value="english">English</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="iepriority">Priority (1-100)</label>
								<select id="iepriority" name="priority" class="form-control" required>
									<?php for ($i=1; $i <= 100; $i++) { ?>	
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>
								</select>
							</div>
							<div class="col-md-6">
								<label for="ieis_active">Status</label>
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
              				<!-- <button id="deletebanner" type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Delete</button> -->
							<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>