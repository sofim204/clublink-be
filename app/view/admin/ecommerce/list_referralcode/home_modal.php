<div id="modal_list_recruiter" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
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
				<div class="row" style="display:flex; align-items:flex-end">
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
					<input type="hidden" id="id_user_recruiter">
					<div class="col-md-2">
						<button id="reset-filter-detail" class="btn btn-block btn-danger">Reset</button>
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
				<div style="margin-bottom: 1rem;"></div>
				<div class="table-responsive">
					<table id="drTableDetailList" class="table table-vcenter table-condensed table-bordered" style="width:100%">
						<thead>
							<tr style="background-color: #FFFFFF;">
								<!-- <th class="text-center">No.</th> -->
								<th>User ID Recruiter</th>
								<th>Name</th>
								<th>Email</th>
								<th>Referral Type</th>
								<th>Register Date</th>
								<th>Register Address</th>
								<!-- <th>Register Location ALL</th> -->
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