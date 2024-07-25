<style>
	.btn-back {
        width: 85px;
        cursor: pointer;
        background: transparent;
        border: 1px solid #999;
        outline: none;
        transition: .5s ease;
    }

    .btn-back.full {
        width: 100%;
    }

    .btn-back:hover {
        transition: .3s ease;
        background: #DD8A0D;
        border: 1px solid transparent;
        color:#FFF;
    }

    .btn-back:hover svg {
        stroke-dashoffset: -480;
    }

    .btn-back span {
        color: white;
        font-size: 18px;
        font-weight: 100;
    }
</style>
<?php
	$admin_foto = '';
	if(isset($sess->admin->foto))$admin_foto = $sess->admin->foto;
	if(empty($admin_foto)) $admin_foto = 'media/pengguna/default.png';
	$admin_foto = base_url($admin_foto);
?>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-6">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin(''); ?>" class="btn btn-default btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="btn-group pull-right">
					<button type="button" id="bprofil_foto" href="#" class="btn btn-info" data-toggle="tooltip" title="Change Display Picture" data-original-title="Change Display Picture"><i class="fa fa-file-image-o"></i> Change Display Picture</button>
					<button type="button" id="bprofil" href="#" class="btn btn-info" data-toggle="tooltip" title="Edit Profile" data-original-title="Edit Profile"><i class="fa fa-edit"></i> Edit Profile</button>
					<button type="button" id="bpassword_change" href="#" class="btn btn-info" data-toggle="tooltip" title="Change Password" data-original-title="Change Password"><i class="fa fa-key"></i> Change Password</button>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Profile</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<?php if(isset($notif)){ ?>
	<div class="alert alert-info" role="alert">
		<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
		<?=$notif?>
	</div>
	<?php } ?>
	<div class="block full row">
		<div class="block-title">
			<h2><strong>Profile</strong></h2>
		</div>
		<div class="form-group">
			<div class="col-md-3">
				<img src="<?=$admin_foto?>" style="width: 100%;" class="img-responsive" />
			</div>
			<div class="col-md-3">&nbsp;</div>
			<div class="col-md-6">
				<div class="table-responsive">
				<table class="table">
					<tr>
						<th>Name</th>
						<td>:</td>
						<td><?=$sess->admin->nama?></td>
					</tr>
					<tr>
						<th>Username</th>
						<td>:</td>
						<td><?=$sess->admin->username?></td>
					</tr>
					<tr>
						<th>Email</th>
						<td>:</td>
						<td><?=$sess->admin->email?></td>
					</tr>
				</table>
				</div>
			</div>
		</div>
	</div>

	<!-- END Content -->
</div>
