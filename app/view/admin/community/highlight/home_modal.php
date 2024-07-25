<style>
	.text-left {
		text-align: left !important;
	}

	tr:hover {background-color: #DADDFC;}
	tr.selected  {
		background-color: #6A72C8;
		color: #ffffff;
	}
</style>

<!-- ================================================================
||                                                                 ||
||                  		OPTIONS MODAL                   	   ||
||                                                                 ||
================================================================= -->
<div id="modal_options_highlight" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Options</strong></h2>
			</div>
			<!-- END Modal Header -->
			<!-- Modal Body -->
			<div class="modal-body">
				<!-- by Muhammad Sofi 21 January 2022 18:38 | comment unused code -->
				<!-- <div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="ainactive" href="#" class="btn btn-primary text-center"><i class="fa fa-ban"></i> Set Inactive?</a>
					</div>
				</div>
				<div class="row" style="margin-bottom: 6px;"></div> -->
				<div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="adelete" href="#" class="btn btn-danger text-center"><i class="fa fa-times-circle"></i> Delete from Highlight?</a>
					</div>
				</div>
				<!-- by Muhammad Sofi 21 January 2022 18:38 | comment unused code -->
				<!-- <div class="row" style="margin-bottom: 6px;"></div> -->
				<!-- <div class="row">
					<div class="col-xs-12 btn-group-vertical " style="text-align: left;">
						<a id="asetSystem" href="#" class="btn btn-info text-center"><i class="fa fa-info-circle"></i> Change to Automatic System?</a>
					</div>
				</div> -->
				<div class="row" style="margin-top: 1em; ">
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
<!-- ================================================================
||                                                                 ||
||                         COMMUNITY LIST                          ||
||                                                                 ||
================================================================= -->
<div id="modal_highlight_community" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Community List</strong></h2>
			</div>
			<!-- END Modal Header -->

			<div class="modal-body">
				<div class="row" style="display:flex; align-items:flex-end">
					<div class="col-md-2">
						<!-- <label for="id_postal_district">Postal District</label> -->
						<input class="form-control" type="hidden" name="id_kelurahan_modal" id="id_kelurahan_modal" style="background:#E7E7E7" disabled>
					</div>
				</div>
			</div>
			<!-- Modal Body -->
			<div class="modal-body">
				<div class="table-responsive">
					<table id="drTableHighlight" class="table table-vcenter table-condensed table-bordered" style="width:100%">
						<thead>
							<tr style="background-color: #FFFFFF;">
								<th class="text-center">#</th>
								<th>Submit Date</th>
								<th>Title</th>
								<th width="650px">Description</th>
								<th width="200px">User</th>
								<th>Status</th>
								<th width="200px">General Location</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
				<div class="form-group form-actions">
					<div class="col-xs-12 text-right">
						<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
						<button id="bbtnAll" type="button" class="btn btn-sm btn-primary">Promote to Highlight</button>
					</div>
				</div>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>