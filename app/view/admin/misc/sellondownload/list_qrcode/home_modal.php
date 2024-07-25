<div id="modal_create_qrcode" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Create QR Code</strong></h2>
			</div>
			<div class="modal-body">
				<form id="form_create_qrcode" action="" method="post" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12">
								<label class="control-label" for="iplace_name">Place Name</label>
								<input type="text" name="place_name" id="iplace_name" class="form-control" autocomplete="off" required/>
							</div>
						</div>
					</fieldset>
					<div class="form-group qrcode_container hidden">
						<div class="col-md-12">
							<center>
								<img id="display_qrcode" src="" class="img-responsive" width="200px" height="200px" style="border: 1px solid #000000" alt="">
							</center>
						</div>
					</div>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="modal_options" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Options</strong></h2>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="bdelete" href="javascript:void(0);" class="btn btn-danger text-center"><i class="fa fa-trash-o"></i> Delete</a>
					</div>
				</div>
				<div class="row" style="margin-bottom: 6px;"></div>
			</div>
		</div>
	</div>
</div>