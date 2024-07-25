<!-- modal add -->
<!-- START by Muhammad Sofi 27 January 2022 16:42 | adding form add data -->
<div id="modal_add" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h2 class="modal-title">Add Common Code</h2>
			</div>
			<div class="modal-body">
				<form id="form_add_data" action="" method="post" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-6">
								<label class="control-label" for="igroup_classified">Group Classified</label>
								<select id="igroup_classified" class="form-control"></select>
							</div>
							<div class="col-md-6">
								<label class="control-label" for="iclassified">Classified</label>
								<input type="text" name="classified" id="iclassified" class="form-control" autocomplete="off" required/>
							</div>
						</div>
						<div class="form-group">
							<!-- <div class="col-md-4">
								<label class="control-label" for="ilast_code">Last Code</label>
								<input type="text" id="ilast_code" class="form-control" autocomplete="off" readonly style="background-color:#F0E8E8" />
							</div> -->
							<div class="col-md-4">
								<label class="control-label" for="icode">Code</label>
								<input type="text" name="code" id="icode" class="form-control" autocomplete="off" />
							</div>
							<div class="col-md-4" style="margin-top: 8px;">
							<label for="iuse_yn">Use y/n</label>
								<select id="iuse_yn" name="use_yn" class="form-control" required>
									<option value="">--Choose--</option>	
									<option value="y">Yes</option>	
									<option value="n">No</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="icodename">Codename</label>
								<input type="text" name="codename" id="icodename" class="form-control" autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iremark">Remark</label>
								<textarea name="remark" id="iremark" class="form-control" rows="1"></textarea>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Save Changes</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!-- END by Muhammad Sofi 27 January 2022 16:42 | adding form add data -->

<!-- modal edit -->
<div id="modal_edit" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h2 class="modal-title">Edit Common Code</h2>
			</div>
			<div class="modal-body">
				<form id="form_edit_data" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<!-- by Muhammad Sofi 3 February 2022 15:34 | add edit variable -->
						<div class="form-group">
							<div class="col-md-12">
								<input type="hidden" id="ieid">
								<label class="control-label" for="ieclassified">Classified</label>
								<input type="text" name="classified" id="ieclassified" class="form-control" autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="control-label" for="iecode">Code</label>
								<input type="text" name="code" id="iecode" class="form-control" autocomplete="off" />
							</div>
							<div class="col-md-6" style="margin-top: 8px;">
							<label for="ieuse_yn">Use y/n</label>
								<select id="ieuse_yn" name="use_yn" class="form-control" required>
									<option value="">--Choose--</option>	
									<option value="y">Yes</option>	
									<option value="n">No</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iecodename">Codename</label>
								<input type="text" name="codename" id="iecodename" class="form-control" autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ieremark">Remark</label>
								<textarea name="remark" id="ieremark" class="form-control" rows="1"></textarea>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="button" id="bdelete" class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i> Delete</button>
							<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Save Changes</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>