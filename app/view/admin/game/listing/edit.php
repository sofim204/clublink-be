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
		<li>Edit</li>
	</ul>
	<!-- END Static Layout Header -->

	<!-- Content -->
	<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="" onsubmit="return false;">
		<div class="block full row">
			<div class="block-title">
				<h2><strong>General</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<label class="" for="iename">Name *</label>
					<input id="iename" type="text" name="name" class="form-control" minlength="1" placeholder="Nama Kategori" autocomplete="off" required />
				</div>
			</div>	
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Type, URL & Version for Mobile</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label for="ietype_for_mobile">Type *</label>
					<select id="ietype_for_mobile" name="tfm" class="form-control">
						<option value="">- Choose Type -</option>
						<option value="app">App</option>
						<option value="web">Web</option>
					</select>
				</div>
				<div class="col-md-4">
					<label class="" for="ieurl_for_mobile">URL</label>
					<input id="ieurl_for_mobile" type="text" name="ufm" class="form-control" minlength="1" placeholder="URL" autocomplete="off" />
				</div>
				<div class="col-md-4">
					<label class="" for="ieversion_for_mobile">Version</label>
					<input id="ieversion_for_mobile" type="text" name="vfm" class="form-control" minlength="1" placeholder="Version" autocomplete="off"/>
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Win</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-6">
					<label class="" for="iewin_cost_ticket">Win Cost Ticket *</label>
					<input id="iewin_cost_ticket" type="number" name="wct" class="form-control" placeholder="Win Cost Ticket" autocomplete="off" required />
				</div>
				<div class="col-md-6">
					<label class="" for="iewin_spt">Win SPT *</label>
					<input id="iewin_spt" type="number" name="wspt" class="form-control" placeholder="Win SPT" autocomplete="off" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Lose</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-6">
					<label class="" for="ielose_cost_ticket">Lose Cost Ticket *</label>
					<input id="ielose_cost_ticket" type="number" name="lct" class="form-control" placeholder="Lose Cost Ticket" autocomplete="off" required />
				</div>
				<div class="col-md-6">
					<label class="" for="ielose_spt">Lose SPT *</label>
					<input id="ielose_spt" type="number" name="lspt" class="form-control" placeholder="Lose SPT" autocomplete="off" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Draw</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-6">
					<label class="" for="iedraw_cost_ticket">Draw Cost Ticket *</label>
					<input id="iedraw_cost_ticket" type="number" name="dct" class="form-control" placeholder="Draw Cost Ticket" autocomplete="off" required />
				</div>
				<div class="col-md-6">
					<label class="" for="iedraw_spt">Draw SPT *</label>
					<input id="iedraw_spt" type="number" name="dspt" class="form-control" placeholder="Draw SPT" autocomplete="off" required />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Misc</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="ieis_active">Active (Android) *</label>
					<select id="ieis_active" name="is_active" class="form-control">
						<option value="1">Yes</option>
						<option value="0">No</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="ieis_active_ios">Active (Ios) *</label>
					<select id="ieis_active_ios" name="is_active_ios" class="form-control">
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
					<button type="submit"  class="btn btn-primary"><i class="fa fa-save"></i> Save Changes</button>
				</div>
			</div>
		</div>
	</form>
</div>
