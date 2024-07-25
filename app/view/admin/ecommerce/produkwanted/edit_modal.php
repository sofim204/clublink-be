<!-- modal user search -->
<div id="user_search_modal" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header text-center">
				<h2 class="modal-title">Search Customer</h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
						<form id="user_search_modal_form" action="<?=base_url_admin("ecommerce/produkwanted/baru/")?>" method="post">
							<div class="form-group">
								<div class="col-md-12">
									<div class="input-group">
										<input id="user_search_modal_input" name="keyword" minlength="2" class="form-control" required />
										<span class="input-group-btn">
											<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
										</span>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<table id="user_search_table" class="table table-bordered">
									<thead>
										<tr>
											<th>ID</th>
											<th>Name</th>
											<th>Email</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td colspan="4">Please input keyword first</td>
										</tr>
									</tbody>
								</table>
							</div>
						</form>
				</div>
				<!-- END Modal Body -->
			</div>
		</div>
	</div>
</div>
