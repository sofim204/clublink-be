<!-- modal add -->
<div id="faq_modal_add" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>New FAQ Section</strong></h2>
			</div>
			<div class="modal-body">
				<form id="ftambahfaq" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>	
						<div class="form-group">
							<!-- by Muhammad Sofi 21 January 2022 20:53 | add insert priority and sort by priority -->
							<div class="col-md-6">
								<label class="" for="ilanguage">Language</label>
								<select id="ilanguage" name="language_id" class="form-control" required>
									<option value="">Choose</option>
									<option value="1">English</option>
									<option value="2">Indonesia</option>
								</select>
							</div>
							<div class="col-md-6">
								<label class="" for="ipriority">Priority (1-100)</label>
								<select id="ipriority" name="priority" class="form-control" required>
									<?php for ($i=0; $i <= 100; $i++) { ?>
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="ititle">Title *</label>
								<!-- <input id="ititle" name="title" type="text" class="form-control" required /> -->
								<textarea id="ititle" name="title" class="ckeditor" rows="1" required></textarea>
							</div>
						</div>
						<div class="form-group">	
							<div class="col-md-12">
								<div class="input-group">
									<label for="icontent">Content *</label>
									<textarea id="icontent" name="content" type="text" class="ckeditor" ></textarea>
								</div>
							</div>
						</div>
					</fieldset>	
					<div class="form-group">
						<div class="col-xs-12 text-right">
							<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- modal edit -->
<div id="modal_edit" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit FAQ</strong></h2>
			</div>
			<div class="modal-body">
				<form id="feditfaq" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">	
							<!-- by Muhammad Sofi 21 January 2022 20:53 | add insert priority and sort by priority -->
							<div class="col-md-6">
								<label class="" for="ielanguage">Language</label>
								<select id="ielanguage" name="language_id" class="form-control" required>
									<option value="1">English</option>
									<option value="2">Indonesia</option>
								</select>
							</div>
							<div class="col-md-6">
								<label class="" for="iepriority">Priority (1-100)</label>
								<select id="iepriority" name="priority" class="form-control" required>
									<?php for ($i=0; $i <= 100; $i++) { ?>
										<option value="<?= $i ?>"><?= $i ?></option>
									<?php  } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ietitle">Title *</label>
								<!-- <input id="ietitle" type="text" name="title" class="form-control" required /> -->
								<textarea id="ietitle" name="title" class="ckeditor" rows="1" required></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iecontent">Content *</label>
								<textarea id="iecontent" name="content" class="ckeditor" rows="5" required></textarea>
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