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
					<a id="aback" href="<?=base_url_admin('redemptionexchange/type/'); ?>" class="btn btn-back"><i class="fa fa-chevron-left"></i> Back</a>
				</div>
			</div>
		</div>
	</div>
	<ul class="breadcrumb breadcrumb-top">
		<li>Admin</li>
		<li>Redemption Exchange</li>
		<li>Type</li>
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
				<div class="col-md-6">
					<label class="" for="ietype">Type *</label>
					<input id="ietype" type="text" name="type" class="form-control" minlength="1" placeholder="Type" autocomplete="off" required />
				</div>
				<div class="col-md-6">
					<label class="" for="iename">Name *</label>
					<input id="iename" type="text" name="name" class="form-control" minlength="1" placeholder="Name" autocomplete="off" required />
				</div>
			</div>	
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Cost SPT, Amount Get & Name Point History</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label class="" for="iecost_spt">Cost SPT *</label>
					<input id="iecost_spt" type="number" name="cs" class="form-control" placeholder="Cost SPT" autocomplete="off" required />
				</div>
				<div class="col-md-4">
					<label class="" for="ieamount_get">Amount Get *</label>
					<input id="ieamount_get" type="number" name="ag" class="form-control" placeholder="Amount Get" autocomplete="off" required />
				</div>
				<div class="col-md-4">
					<label class="" for="iename_point_history">Name Point History</label>
					<input id="iename_point_history" type="text" name="nph" class="form-control" placeholder="Name Point History" autocomplete="off" />
				</div>
			</div>
		</div>

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Misc</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="ieis_active">Active *</label>
					<select id="ieis_active" name="is_active" class="form-control">
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
