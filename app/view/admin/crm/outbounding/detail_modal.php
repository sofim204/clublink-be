<style>
	.text-left {
		text-align: left !important;
	}
</style>
<!-- modal tambah -->

<!-- modal edit -->
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit Detail Outbound</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="ieid">Detail Outbound ID</label>
								<input id="ieid" type="number" name="id" class="form-control" disabled>
							</div>
							<div class="col-md-6">
								<label class="" for="ieoutboundid">Outbound ID</label>
								<input id="ieoutboundid" type="number" name="c_outbound_id" class="form-control" disabled>
							</div>
						</div>
						<div class="form-group">
				  		  	<div class="col-md-6">
								<label class="" for="iname">Name</label>
								<input id="iname" type="text" name="name" class="form-control">
							</div>
							<div class="col-md-6">
								<label class="" for="iurl">Link</label>
								<input id="iurl" type="text" name="url" class="form-control">
							</div>
				  		</div>
				  		<div class="form-group">
							<div class="col-md-12">
								<label class="" for="itype">Type</label>
								<select id="itype" name="type" class="form-control" required>
									<option value="product">Product</option>
									<option value="shop">Shop</option>
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
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="aedit" href="#" class="btn btn-info text-left"><i class="fa fa-pencil"></i> Edit</a>
						<button id="bhapus" type="button" class="btn btn-danger btn-block text-left"><i class="fa fa-trash-o"></i> Delete</button>
					</div>
				</div>
				<div class="row" style="margin-top: 1em;">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					</div>
				</div>
				<!-- END Modal Body -->
			</div>
		</div>
	</div>
</div>
