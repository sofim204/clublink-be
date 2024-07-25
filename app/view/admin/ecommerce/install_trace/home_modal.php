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
				<!-- <div class="row" style="display:flex; align-items:flex-end">
					<div class="col-md-3">
						<label class="control-label" for="filter_referral_type">Filter</label>
						<select name="referral_type" id="filter_referral_type" class="form-control">
							<option value="">All</option>
							<option value="My Share">My Share</option>
							<option value="Community Detail">Community Detail</option>
							<option value="Product Detail">Product Detail</option>
							<option value="Shop">Shop</option>
						</select>
					</div>
				</div> -->
                <fieldset>
					<div class="form-group" style="margin-bottom: 10px;">
						<div class="col-md-6">
                            <label class="control-label" for="detail_referral">Referral</label>
                            <input type="text" id="detail_referral" class="form-control" style="background-color: #DBD5D1;" />
						</div>
						<!-- <div class="col-md-4">
							<label class="control-label" for="detail_date">Date</label>
							<input type="text" name="cdate" id="detail_date" class="form-control" style="background-color: #DBD5D1; pointer-events: none;" />
						</div>
						<div class="col-md-4">
							<label class="control-label" for="detail_total">Total</label>
							<input type="text" name="" id="detail_total" class="form-control" style="background-color: #DBD5D1; pointer-events: none;" />
						</div> -->
					</div>
				</fieldset>
				<div style="margin-bottom: 1rem;"></div>
				<div class="table-responsive">
					<table id="drTableDetail" class="table table-vcenter table-condensed table-bordered" style="width:100%">
						<thead>
							<tr style="background-color: #FFFFFF;">
                                <th width="20px">No</th>
                                <th>Create Date</th>
                                <th>Is Downloaded</th>
                                <th>cdate downloaded</th>
                                <th>Is Registered</th>
                                <th>cdate registered</th>
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