<style>
	.btn-back {
        width: 85px;
        cursor: pointer;
        background: #F9F5F5;
        border: 1px solid #999;
        outline: none;
		color: #222121;
        transition: .3s ease;
    }

    .btn-back:hover {
        transition: .3s ease;
        background: #DD8A0D;
        border: 1px solid transparent;
        color:#FFF;
    }
</style>
<div id="page-content">
	<!-- Static Layout Header -->
	<div class="content-header">
		<div class="row" style="padding: 0.5em 2em;">
			<div class="col-md-12">
				<div class="btn-group">
					<a id="aback" href="<?=base_url_admin('game/listing/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Game</li>
		<li>New</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="ftambah" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>General</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<label class="" for="iname">Name *</label>
					<input id="iname" type="text" name="name" class="form-control" minlength="1" placeholder="Game Name" autocomplete="off" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Icon *</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<input id="iimage_icon" name="image_icon" type="file" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Type, URL & Version For Mobile</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="itfm">Type *</label>
					<select id="itfm" name="tfm" class="form-control">
						<option value="">- Choose Type -</option>
						<option value="app">App</option>
						<option value="web">Web</option>
					</select>
				</div>
				<div class="col-md-4">
					<label class="" for="iufm">URL</label>
					<input id="iufm" type="text" name="ufm" class="form-control" minlength="1" placeholder="URL" autocomplete="off" />
				</div>	
				<div class="col-md-4">
					<label class="" for="ivfm">Version</label>
					<input id="ivfm" type="text" name="vfm" class="form-control" minlength="1" placeholder="Version" autocomplete="off"/>
				</div>			
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Win</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-6">
					<label class="" for="iwct">Win Cost Ticket *</label>
					<input id="iwct" type="number" name="wct" class="form-control" placeholder="Win Cost Ticket" autocomplete="off" required />
				</div>
				<div class="col-md-6">
					<label class="" for="iwspt">Win SPT *</label>
					<input id="iwspt" type="number" name="wspt" class="form-control" placeholder="Win SPT" autocomplete="off" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Lose</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-6">
					<label class="" for="ilct">Lose Cost Ticket *</label>
					<input id="ilct" type="number" name="lct" class="form-control" placeholder="Lose Cost Ticket" autocomplete="off" required />
				</div>
				<div class="col-md-6">
					<label class="" for="ilspt">Lose SPT *</label>
					<input id="ilspt" type="number" name="lspt" class="form-control" placeholder="Lose SPT" autocomplete="off" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Draw</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-6">
					<label class="" for="idct">Draw Cost Ticket *</label>
					<input id="idct" type="number" name="dct" class="form-control" placeholder="Draw Cost Ticket" autocomplete="off" value="0" required />
				</div>
				<div class="col-md-6">
					<label class="" for="idspt">Draw SPT *</label>
					<input id="idspt" type="number" name="dspt" class="form-control" placeholder="Draw SPT" autocomplete="off" value="0" required />
				</div>
			</div>
		</div>		

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Misc</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="iis_active">Active (Android) *</label>
					<select id="iis_active" name="is_active" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="iis_active_ios">Active (Ios) *</label>
					<select id="iis_active_ios" name="is_active_ios" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Action</strong></h2>
			</div>
			<div class="form-group form-actions">
				<div class="col-xs-12 text-right">
					<button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
				</div>
			</div>
		</div>

	</form>
</div>
