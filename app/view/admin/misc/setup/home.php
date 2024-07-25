<style>
	.file-input {
		display: block;
		text-align: left;
		margin-left: -15px;
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
<div id="page-content">
	<!-- Static Layout Header -->
	
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Misc</li>
		<!-- <li>Setup</li> -->
		<li>Settings</li>
	</ul>
	<!-- END Static Layout Header -->
	
	<!-- Content -->
	
	<div class="row">
		<div class="col-md-6">
			<!-- start block app_bank -->
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<?php if($bank_count==0){ ?>
							Please add <a href="<?=base_url_admin("misc/bank/")?>">Bank List</a> first.
						<?php }else{ ?>
							<form action="<?=base_url('api_admin/misc/setup/')?>app_config" method="post" class="form-horizontal form-setup" >
								<div class="form-group">
									<div class="col-md-5">
										<label for="fs_app_config_remark_C0" class="control-label">Current Bank</label>
									</div>
									<div class="col-md-7">
										<div class="input-group">
											<select id="fs_app_config_remark_C0" name="app_config_remark_C0" class="form-control">
												<?php foreach($bank_list as $bl){ ?>
													<option value="<?=$bl->id?>" <?php if($fs_app_config_remark_C0 == $bl->id) echo 'selected'; ?>><?=$bl->nama?></option>
												<?php } ?>
											</select>
											<div class="input-group-btn">
												<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
											</div>
										</div>
										
									</div>
								</div>
							</form>
						<?php } ?>
					</div>
				</div>
			</div>
			<!-- end block app_bank  -->
			
			<!-- start block selling_fee -->
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>product_fee" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_product_fee_remark_F7" class="control-label">Selling Fee Percentage</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_product_fee_remark_F7" name="product_fee_remark_F7" type="number" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<!-- start block pg_fee -->
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>product_fee" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_product_fee_remark_F6" class="control-label">MDR VAT Percentage</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_product_fee_remark_F6" name="product_fee_remark_F6" type="number" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- end block product_fee  -->

			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>app_config_maintenance" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C2" class="control-label">Maintenance Page</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<select id="fs_app_config_remark_C2" name="app_config_remark_C2" class="form-control">
											<option value="on">ON</option>
											<option value="off">OFF</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="block" style="display: none;">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>protection_product" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C3" class="control-label">Protection Product</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<select id="fs_app_config_remark_C3" name="app_config_remark_C3" class="form-control">
											<option value="on">ON</option>
											<option value="off">OFF</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- start block Default Setting Home GNB -->
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>default_setting_gnb" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C5" class="control-label">Default Setting Home GNB</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<select id="fs_app_config_remark_C5" name="app_config_remark_C5" class="form-control">
											<option value="all">All</option>
											<option value="province">Province</option>
											<option value="city">City</option>
											<option value="district">District</option>
											<option value="neighborhood">Neighborhood</option>
											<option value="samestreet">Samestreet</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
									
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- end block Default Setting Home GNB  -->

			<!-- start block Total Allowed in same device -->
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>total_allowed_account_in_same_device" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C6" class="control-label">Total Allowed Account in Same Device</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_app_config_remark_C6" name="app_config_remark_C6" type="number" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- end block Total Allowed in same device  -->

			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>app_config_wallet_active" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C7" class="control-label">Wallet Active</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<select id="fs_app_config_remark_C7" name="app_config_remark_C7" class="form-control">
											<option value="on">ON</option>
											<option value="off">OFF</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- start block Total Allowed in same IP -->
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>total_allowed_account_in_same_ip" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C8" class="control-label">Total Allowed Account in Same IP</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_app_config_remark_C8" name="app_config_remark_C8" type="number" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- end block Total Allowed in same IP  -->

			<!-- start block Share Sellon Image -->
			<div class="block" id="one_block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>share_sellon_image" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C9" class="control-label">Share Sellon Image <span style="color:grey">(click below to see image)</span></label>
									<input type="text" id="fs_app_config_remark_C9" class="form-control" style="cursor: pointer;" onclick="showImage()"/>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<div class="col-md-12">
											<div class="file-input">
												<input id="" type="file" name="app_config_remark_C9" accept=".jpg, .jpeg, .png, .gif" required />
												<span class="button">Choose</span>
												<span class="label" data-js-label>No file selected</label>
											</div>
										</div>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- end block Share Sellon Image  -->

			<!-- start Facebook Login -->
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>facebook_login" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C14" class="control-label">Facebook Login</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<select id="fs_app_config_remark_C14" name="app_config_remark_C14" class="form-control">
											<option value="on">ON</option>
											<option value="off">OFF</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- end Facebook Login -->

			<!-- start show ads at array X on every page(product and community) -->
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>show_ads_on_every_page" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C15" class="control-label">Show ads at array X on every page(product and community)</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_app_config_remark_C15" name="app_config_remark_C15" type="number" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- end show ads at array X on every page(product and community) -->

			<!-- start show ads after play game X times -->
			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>show_ads_after_play_game" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C16" class="control-label">Show ads after play game X times</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<input id="fs_app_config_remark_C16" name="app_config_remark_C16" type="number" class="form-control" />
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- end show ads after play game X times -->

			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>app_config_singapore_server" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C23" class="control-label">Singapore Server Status</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<select id="fs_app_config_remark_C23" name="app_config_remark_C23" class="form-control">
											<option value="on">ON</option>
											<option value="off">OFF</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>register_via_email_need_verif_phone" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C24" class="control-label">Register Via Email Need Verif Phone</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<select id="fs_app_config_remark_C24" name="app_config_remark_C24" class="form-control">
											<option value="on">ON</option>
											<option value="off">OFF</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>register_via_phone_number_need_verif_phone" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C25" class="control-label">Register Via Phone Number Need Verif Phone</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<select id="fs_app_config_remark_C25" name="app_config_remark_C25" class="form-control">
											<option value="on">ON</option>
											<option value="off">OFF</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="block">
				<div class="row">
					<div class="col-md-12">
						<form action="<?=base_url('api_admin/misc/setup/')?>dynamic_setting_wallet_access" method="post" class="form-horizontal form-setup" >
							<div class="form-group">
								<div class="col-md-5">
									<label for="fs_app_config_remark_C26" class="control-label">Wallet Access(web/inapp)</label>
								</div>
								<div class="col-md-7">
									<div class="input-group">
										<select id="fs_app_config_remark_C26" name="app_config_remark_C26" class="form-control">
											<option value="webview">webview</option>
											<option value="inapp">inapp</option>
										</select>
										<div class="input-group-btn">
											<button type="submit" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div> <!-- end left -->
		<div class="col-md-6">
		</div><!-- end right -->
	</div>
	<!-- END Content -->
</div>
<script type="text/javascript">
function showImage() {
	window.open($("#fs_app_config_remark_C9").val());
}
</script>