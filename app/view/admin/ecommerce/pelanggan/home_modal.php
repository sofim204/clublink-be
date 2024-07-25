<style>
	.text-left {
		text-align: left !important;
	}
	.purple-color-menu{
		background: #bb6bd9;
		color: #ffffff;
		border: 1px solid #ab43d2;
	}
	.purple-color-menu:hover{
		background: #ab43d2;
		color: #ffffff;
	}
	.yellow-color-menu{
		background: #f2c94c;
		color: #ffffff;
		border: 1px solid #eab615;
	}
	.yellow-color-menu:hover{
		background: #eab615;
		color: #ffffff;
	}
	.row .btn-group-vertical > button {
		margin-bottom: 5px;
	}
</style>
<!-- modal option -->
<div id="modal_option" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Options</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">

				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<button id="adetail" href="#" type="button" class="btn btn-info text-left"><i class="fa fa-info-circle"></i> Details</button>
						<button id="bemail_lupa" type="button" class="btn btn-info text-left"><i class="fa fa-key"></i> Forgot Password</button>
						<!--<button id="transaction" type="button" class="btn text-left purple-color-menu"><i class="fa fa-file-text-o"></i> Transaction List</button>-->
						<!--<button id="cbuyer" type="button" class="btn text-left yellow-color-menu"><i class="fa fa-file-text-o"></i> Product List as Buyer</button>-->
						<!--<button id="cseller" type="button" class="btn btn-warning text-left"><i class="fa fa-file-text-o"></i> Product List as Seller</button>-->
						<!-- <button id="bactivated" type="button" class="btn btn-success text-left"><i class="fa fa-play"></i> Set Active</button>
						<button id="bdeactivated" type="button" class="btn btn-danger text-left"><i class="fa fa-stop"></i> Set inactive</button> -->
						<button id="b_change_verif_status" type="button" class="btn btn-primary text-left"><i class="fa fa-check-circle"></i> Change Verification Status</button>
						<button id="b_change_status" type="button" class="btn btn-info text-left"><i class="fa fa-toggle-on"></i> Change Status (Active/Inactive)</button>
						<button id="b_change_status_permanent_inactive" type="button" class="btn btn-info text-left"><i class="fa fa-check-circle"></i> Permanent Acc Stop</button>
						<?php if($user_alias == "admin_dev") { ?>
							<button id="b_delete_account" type="button" class="btn btn-danger text-left"><i class="fa fa-times-circle"></i> Delete Account?</button>
						<?php } ?>
						<?php if($user_role == "admin") { ?>
							<button id="b_change_status_admin" type="button" class="btn btn-primary text-left"><i class="fa fa-star"></i> Change Admin Status</button>
						<?php } ?>
					</div>
				</div>
				<div class="row" style="margin-top: 1em;">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times-circle"></i> Close</button>
					</div>
				</div>

			</div><!-- END Modal Body -->

		</div>
	</div>
</div>

<!-- modal tambah -->
<div id="modal_tambah" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">New Customer</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="ifnama">Full Name*</label>
								<input id="ifnama" type="text" name="fnama" class="form-control" minlength="1" placeholder="Full Name" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="iemail">Email *</label>
								<input id="iemail" type="text" name="email" class="form-control" minlength="" placeholder="" />
							</div>
							<div class="col-md-6">
								<label for="itelp">Phone Number *</label>
								<input id="itelp" type="text" name="telp" class="form-control" minlength="" placeholder="" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ipassword">Password *</label>
								<input id="ipassword" name="password" type="text" class="form-control" minlength="1" placeholder="" value="123456" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ikelamin">Gender</label>
								<select id="ikelamin" name="kelamin" class="form-control" rows="3">
									<option value="1">Male</option>
									<option value="0">Female</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="ibdate">Birth Date</label>
								<input id="ibdate" name="bdate" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" minlength="1" value="1975-01-01" placeholder="" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="ialamat">Address</label>
								<textarea id="ialamat" class="form-control" name="alamat" rows="3"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="control-label" for="iprovinsi">Province</label>
								<input id="iprovinsi" name="provinsi" type="text" class="form-control" />
							</div>
							<div class="col-md-6">
								<label class="control-label" for="ikabkota">City</label>
								<input id="ikabkota" name="kabkota" type="text" class="form-control" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6">
								<label class="control-label" for="ikecamatan">District</label>
								<input id="ikecamatan" name="kecamatan" type="text" class="form-control" />
							</div>
							<div class="col-md-6">
								<label for="ikodepos">PostalCode</label>
								<input id="ikodepos" type="text" name="kodepos" class="form-control" minlength="1" placeholder="" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6">
								<label class="control-label" for="ilatitude">Latitude</label>
								<input id="ilatitude" name="latitude" type="text" class="form-control" />
							</div>
							<div class="col-md-6">
								<label class="control-label" for="ilongitude">Longitude</label>
								<input id="ilongitude" name="longitude" type="text" class="form-control" minlength="1" placeholder="" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-4">
								<label for="iis_active">Active</label>
								<select id="iis_active" name="is_active" class="form-control">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save Changes</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- modal edit -->
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Edit</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="iefnama">Full Name*</label>
								<input id="iefnama" type="text" name="fnama" class="form-control" minlength="1" placeholder="Full Name" required />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ieemail">Email *</label>
								<input id="ieemail" type="text" name="email" class="form-control" minlength="" placeholder="" />
							</div>
							<div class="col-md-6">
								<label for="ietelp">Phone Number *</label>
								<input id="ietelp" type="text" name="telp" class="form-control" minlength="" placeholder="" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="iepassword">Password *</label>
								<input id="iepassword" name="password" type="text" class="form-control" minlength="1" placeholder="" value="123456" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label for="iekelamin">Gender</label>
								<select id="iekelamin" name="kelamin" class="form-control" rows="3">
									<option value="1">Male</option>
									<option value="0">Female</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="iebdate">Birth Date</label>
								<input id="iebdate" name="bdate" type="text" class="form-control input-datepicker" data-date-format="yyyy-mm-dd" minlength="1" value="1975-01-01" placeholder="" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iealamat">Address</label>
								<textarea id="iealamat" class="form-control" name="alamat" rows="3"></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-6">
								<label class="control-label" for="ieprovinsi">Province</label>
								<input id="ieprovinsi" name="provinsi" type="text" class="form-control" />
							</div>
							<div class="col-md-6">
								<label class="control-label" for="iekabkota">City</label>
								<input id="iekabkota" name="kabkota" type="text" class="form-control" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6">
								<label class="control-label" for="iekecamatan">District</label>
								<input id="iekecamatan" name="kecamatan" type="text" class="form-control" />
							</div>
							<div class="col-md-6">
								<label class="control-label" for="iekodepos">Postal Code</label>
								<input id="iekodepos" type="text" name="kodepos" class="form-control" minlength="1" placeholder="" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6">
								<label class="control-label" for="ielatitude">Latitude</label>
								<input id="ielatitude" name="latitude" type="text" class="form-control" />
							</div>
							<div class="col-md-6">
								<label class="control-label" for="ielongitude">Longitude</label>
								<input id="ielongitude" name="longitude" type="text" class="form-control" minlength="1" placeholder="" />
							</div>
						</div>

					</fieldset>

					<fieldset>
						<div class="form-group">
							<div class="col-md-6">
								<label for="ieis_active">Active</label>
								<select id="ieis_active" name="is_active" class="form-control">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="bhapus" type="button" class="btn btn-sm btn-warning"><i class="fa fa-trash-o"></i> Delete</button>
							<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save"></i> Save Changes</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- modal redeem poin -->
<div id="modal_redeem_poin" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Redeem Poin</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fredeem_poin" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label for="ipoin_pelanggan">Poin Sekarang: <span id="ipoin_pelanggan">0</span> poin</label>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<div class="form-group">
							<label for="inominal_poin" class="col-md-4">Redeem</label>
							<div class=" col-md-8">
								<input id="inominal_poin" type="text" class="form-control" value="0" placeholder="" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="isisa_poin">Sisa Poin: <span id="isisa_poin">0</span> poin</label>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Redeem</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- modal image change -->
<div id="modal_image_change" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Change Profile Picture</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fimage_change" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="" for="ieimage_icon">Choose Image File * <small>300px x 300px</small></label>
								<input id="ieimage_icon" type="file" name="image_icon" class="form-control" accept=".png|.jpg|.jpeg"  required />
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

<div id="modal_change_verification_status" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h3 class="modal-title"><strong>Change Verification</strong></h3>
			</div>

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="form_change_verification_status" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label for="user_id">User ID : </label>
								<input type="text" name="" id="user_id" class="form-control" style="background-color: #DBD5D1; pointer-events: none;">
							</div>
							<div class="col-md-8">
								<label for="user_email">Email : </label>
								<input type="text" name="" id="user_email" class="form-control" style="background-color: #DBD5D1;">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="status_email_active">Email Verification : </label>
								<select id="status_email_active" name="is_confirmed" class="form-control">
									<option value="1">Active</option>
									<option value="0">Inactive</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- permanent inactive -->
<div id="modal_change_status_permanent_inactive" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h3 class="modal-title"><strong>Permanently Account Stop</strong></h3>
			</div>

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="form_change_status_permanent_inactive" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label for="user_id">User ID : </label>
								<input type="text" name="" id="user_id" class="form-control" style="background-color: #DBD5D1; pointer-events: none;">
							</div>
							<div class="col-md-8">
								<label for="user_email">Email : </label>
								<input type="text" name="" id="user_email" class="form-control" style="background-color: #DBD5D1;">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="status_permanent_inactive">Status : </label>
								<select id="status_permanent_inactive" name="is_permanent_inactive" class="form-control">
									<option value="1">No</option>
									<option value="0">Yes</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="iinactive_text">Reason : </label>
								<input type="text" name="inactive_text" id="iinactive_text" class="form-control" autocomplete="off">
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<div id="modal_change_status" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h3 class="modal-title"><strong>Change Status</strong></h3>
			</div>

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="form_change_status" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label for="user_id">User ID : </label>
								<input type="text" name="" id="user_id_status" class="form-control" style="background-color: #DBD5D1; pointer-events: none;">
							</div>
							<div class="col-md-8">
								<label for="user_email">Email : </label>
								<input type="text" name="" id="user_email_status" class="form-control" style="background-color: #DBD5D1;">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="status_user_active">User Active : </label>
								<select id="status_user_active" name="is_active" class="form-control">
									<option value="1">Active</option>
									<option value="0">Inactive</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<div id="modal_change_status_admin" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h3 class="modal-title"><strong>Change Status</strong></h3>
			</div>

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="form_change_status_admin" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-4">
								<label for="user_id">User ID : </label>
								<input type="text" name="" id="user_id_status" class="form-control" style="background-color: #DBD5D1; pointer-events: none;">
							</div>
							<div class="col-md-8">
								<label for="user_email">Email : </label>
								<input type="text" name="" id="user_email_status" class="form-control" style="background-color: #DBD5D1;">
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-12">
								<label for="status_as_admin">Admin Status : </label>
								<select id="status_as_admin" name="is_admin" class="form-control">
									<option value="1">Yes</option>
									<option value="0">No</option>
								</select>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-primary">Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>