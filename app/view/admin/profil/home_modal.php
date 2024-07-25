<!-- modal option -->
<div id="modal_profil_foto" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Change Display Picture</strong></h2>
			</div>
			<!-- END Modal Header -->

			<div class="modal-body">
				<form id="fmodal_profil_foto" method="post" enctype="multipart/form-data" action="<?=base_url_admin('profil/edit_foto')?>" class="form-horizontal">
					<div class="form-group">
						<input id="iprofil_foto" type="file" name="foto" class="form-control" required />
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<button type="submit" class="btn btn-success pull-right"><i class="fa fa-upload"></i> Upload Image</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- modal profil edit -->
<div id="modal_profil_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit Profile</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fmodal_profil_edit" method="post" enctype="multipart/form-data" action="<?=base_url_admin('profil/edit')?>" class="form-horizontal">
					<div class="form-group">
						<div class="col-md-12">
							<label for="imodal_profil_edit_nama">Name *</label>
							<input id="imodal_profil_edit_nama" type="text" name="nama" class="form-control" value="<?=$sess->admin->nama?>" required />
						</div>
						<div class="col-md-12">
							<label for="imodal_profil_edit_email">Email *</label>
							<input id="imodal_profil_edit_email" type="text" name="email" class="form-control" value="<?=$sess->admin->email?>" required />
						</div>
						<div class="col-md-12">
							<label for="imodal_profil_edit_username">Username *</label>
							<input id="imodal_profil_edit_username" type="text" name="username" class="form-control" minlength="6" value="<?=$sess->admin->username?>" required />
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<button type="submit" value="Submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save Changes</button>
						</div>
					</div>
				</form>
			</div>
			<!-- Modal Body -->

		</div>
	</div>
</div>
<!-- end modal profil edit -->

<!-- modal profil edit -->
<div id="modal_password_change" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Change Password</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fmodal_password_change" method="post" enctype="multipart/form-data" action="<?=base_url_admin('profil/edit')?>" class="form-horizontal">
					<div class="form-group">
						<div class="col-md-12">
							<label for="imodal_password_change_oldpassword">Old Password *</label>
							<input id="imodal_password_change_oldpassword" type="password" name="oldpassword" class="form-control" value="" required />
						</div>
						<div class="col-md-12">
							<label for="imodal_password_change_newpassword">New Password *</label>
							<input id="imodal_password_change_newpassword" type="password" name="newpassword" class="form-control" minlength="6" value="" required />
						</div>
						<div class="col-md-12">
							<label for="imodal_password_change_confirm_newpassword">Confirm New Password *</label>
							<input id="imodal_password_change_confirm_newpassword" type="password" name="confirm_newpassword" class="form-control" minlength="6" value="" required />
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<button type="submit" value="Submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save Changes</button>
						</div>
					</div>
				</form>
			</div>
			<!-- Modal Body -->

		</div>
	</div>
</div>
<!-- end modal profil edit -->
