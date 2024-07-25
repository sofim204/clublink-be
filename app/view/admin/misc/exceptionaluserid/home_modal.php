<!-- modal add -->
<!-- START by Muhammad Sofi 27 January 2022 16:42 | adding form add data -->
<div id="modal_add" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h2 class="modal-title">Add User ID</h2>
			</div>
			<div class="modal-body">
				<form id="form_add_data" action="" method="post" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<!-- <div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="user_id_sg">user_id SG</label>
								<input id="user_id_sg" name="b_user_id_sg" type="text" class="form-control" autocomplete="off" required/>
							</div>
						</div> -->
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="user_id_id">B User ID</label>
								<input id="user_id_id" name="b_user_id_id" type="text" class="form-control" autocomplete="off" required/>
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
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-center">
				<h2 class="modal-title">Edit User ID</h2>
			</div>
			<div class="modal-body">
				<form id="form_edit_data" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<!-- by Muhammad Sofi 3 February 2022 15:34 | add edit variable -->
						<!-- <div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="edit_user_id_sg">user_id SG</label>
								<input id="edit_user_id_sg" name="b_user_id_sg" type="text" class="form-control" autocomplete="off" required/>
							</div>
						</div> -->
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="edit_user_id_id">B User ID</label>
								<input id="edit_user_id_id" name="b_user_id_id" type="text" class="form-control" autocomplete="off" required/>
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