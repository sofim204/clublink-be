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
		<li>Setting</li>
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
				<div class="col-md-6">
					<label class="" for="itype">Type *</label>
					<input id="itype" type="text" name="type" class="form-control" minlength="1" placeholder="Type" autocomplete="off" required />
					<label class="" for="itype"><p style="color: #999;">Recommendation : <?php foreach($types as $type){echo $type->type.' || ';}?></p></label>
				</div>
				<div class="col-md-6">
					<label class="" for="iname">Name *</label>
					<input id="iname" type="text" name="name" class="form-control" minlength="1" placeholder="Name" autocomplete="off" required />
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
				<h2><strong>Cost SPT, Amount Get & Name Point History</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-4">
					<label class="" for="ics">Cost SPT *</label>
					<input id="ics" type="number" name="cs" class="form-control" placeholder="Cost SPT" autocomplete="off" required />
				</div>
				<div class="col-md-4">
					<label class="" for="iag">Amount Get *</label>
					<input id="iag" type="number" name="ag" class="form-control" placeholder="Amount Get" autocomplete="off" required />
				</div>
				<div class="col-md-4">
					<label class="" for="inph">Name Point History</label>
					<input id="inph" type="text" name="nph" class="form-control" placeholder="Name Point History" autocomplete="off" />
				</div>
			</div>
		</div>	

		<div class="block full row">
			<div class="block-title">
				<h2><strong>Misc</strong></h2>
			</div>
			<div class="form-group">
				<div class="col-md-3">
					<label for="iis_active">Active *</label>
					<select id="iis_active" name="is_active" class="form-control">
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
