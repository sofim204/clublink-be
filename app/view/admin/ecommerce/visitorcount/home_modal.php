<style>
	.text-left {
		text-align: left !important;
	}
</style>

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
						<a id="adetail" href="#" class="btn btn-info text-left" target="_blank">
							<i class="fa fa"></i> View Transaction Detail <i class="fa fa-external-link"></i>
						</a>
						<a id="arefund" href="#" class="btn btn-danger text-left">
							<i class="fa "></i> Refund
						</a>
						<!--<a id="aabort" href="#" class="btn btn-info text-left">
							<i class="fa "></i> Abort Cancellation
						</a>-->
					</div>
				</div>
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

<div id="modal_history" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>History</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<fieldset>
					<!-- by Muhammad Sofi 3 February 2022 15:34 | add edit variable -->
					<div class="form-group" style="margin-bottom: 10px;">
						<div class="col-md-4">
							<label class="control-label" for="detail_mobile_type">Mobile Type</label>
							<input type="text" name="mobile_type" id="detail_mobile_type" class="form-control" style="background-color: #DBD5D1; pointer-events: none;" />
						</div>
						<div class="col-md-4">
							<label class="control-label" for="detail_date">Date</label>
							<input type="text" name="cdate" id="detail_date" class="form-control" style="background-color: #DBD5D1; pointer-events: none;" />
						</div>
						<div class="col-md-4">
							<label class="control-label" for="detail_total">Total</label>
							<input type="text" name="" id="detail_total" class="form-control" style="background-color: #DBD5D1; pointer-events: none;" />
							<!-- <div class="col-md-3">
								<label>Total Android = <span id ='totalAndroid'>0</span> </label>
							</div> -->
						</div>
					</div>
				</fieldset>
				<div style="margin-bottom: 1rem;"></div>
				<div class="table-responsive">
					<table id="drTableDetailLog" class="table table-vcenter table-condensed table-bordered" style="width:100%">
						<thead>
							<tr style="background-color: #FFFFFF;">
								<th class="text-center">No.</th>
								<th>Nama</th>
								<th>Date Time</th>
								<th>Mobile Type</th>
								<th>Email</th>
								<!-- <th>Latlong</th> -->
								<!-- <th>Location</th> -->
								<!-- <th>Province</th> -->
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
