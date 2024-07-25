<!-- modal add -->
<!-- START by Muhammad Sofi 27 January 2022 16:42 | adding form add data -->
<div id="modal_add" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add Multi Language</strong></h2>
			</div>
			<div class="modal-body">
				<form id="form_add_data" action="" method="post" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
							<label for="iftype_multilanguage">Type Multilanguage</label>
								<select id="iftype_multilanguage" name="type" class="form-control">
									<option value="mobile">Mobile</option>	
									<option value="api">Api</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ivariable_name">Variable Name</label>
								<input type="text" name="variable_name" id="ivariable_name" class="form-control" autocomplete="off" required/>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iindonesia">Indonesia</label>
								<!-- <input type="text" name="indonesia" id="iindonesia" class="form-control" autocomplete="off" /> -->
								<textarea name="indonesia" id="iindonesia" class="form-control" rows="2"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ienglish">English</label>
								<!-- <input type="text" name="english" id="ienglish" class="form-control" autocomplete="off" /> -->
								<textarea name="english" id="ienglish" class="form-control" rows="2"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
									<label class="control-label" for="ikorea">Korea</label>
									<!-- <input type="text" name="korea" id="ikorea" class="form-control" autocomplete="off" /> -->
									<textarea name="korea" id="ikorea" class="form-control" rows="2"></textarea>
								</div>
							</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ithailand">Thailand</label>
								<!-- <input type="text" name="thailand" id="ithailand" class="form-control" autocomplete="off" /> -->
								<textarea name="thailand" id="ithailand" class="form-control" rows="2"></textarea>
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
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit Multi Language</strong></h2>
			</div>
			<div class="modal-body">
				<form id="form_edit_data" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<!-- by Muhammad Sofi 3 February 2022 15:34 | add edit variable -->
						<div class="form-group">
							<div class="col-md-12">
								<input type="hidden" id="ieid">
								<label class="control-label" for="ievariable_name">Variable Name</label>
								<input type="text" name="variable_name" id="ievariable_name" class="form-control" autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ieindonesia">Indonesia</label>
								<!-- <input type="text" name="indonesia" id="ieindonesia" class="form-control" autocomplete="off" /> -->
								<textarea name="indonesia" id="ieindonesia" class="form-control" rows="2"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ieenglish">English</label>
								<!-- <input type="text" name="english" id="ieenglish" class="form-control" autocomplete="off" /> -->
								<textarea name="english" id="ieenglish" class="form-control" rows="2"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
									<label class="control-label" for="iekorea">Korea</label>
									<!-- <input type="text" name="korea" id="iekorea" class="form-control" autocomplete="off" /> -->
									<textarea name="korea" id="iekorea" class="form-control" rows="2"></textarea>
								</div>
							</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iethailand">Thailand</label>
								<!-- <input type="text" name="thailand" id="iethailand" class="form-control" autocomplete="off" /> -->
								<textarea name="thailand" id="iethailand" class="form-control" rows="2"></textarea>
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