<div id="modal_detail" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Detail</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
                <fieldset>
					<div class="form-group" style="margin-bottom: 10px;">
						<div class="row" style="display:flex; align-items:start">
							<div class="col-md-6">
								<label class="control-label" for="detail_udid">UDID</label>
								<input type="text" id="detail_udid" class="form-control" style="background-color: #DBD5D1;" />
							</div>
						</div>
					</div>
				</fieldset>
				<div style="margin-bottom: 1rem;"></div>
				<div class="table-responsive">
					<table id="drTableDetail" class="table table-vcenter table-condensed table-bordered" style="width:100%">
						<thead>
							<tr style="background-color: #FFFFFF;">
                                <th width="10px">No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Create Date</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>