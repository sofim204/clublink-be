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
						<button id="adetail" type="button" class="btn btn-info text-left"><i class="fa fa-info-circle"></i> Detail</button>
						<button id="button_add_to_popular_club_homepage" type="button" class="btn btn-info text-left"><i class="fa fa-info-circle"></i> Add To Popular Club</button>
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

<div id="modal_add_popular_club_to_homepage" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width: 600px; margin-top: 200px;">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Add</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="form_add_popular_club_to_homepage" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-8">
								<label class="" for="select_popular_club">Club Name</label><br>
								<input id="club_name" type="text" class="form-control" size="40" autocomplete="off" disabled/>

								<input id="club_choosed_id" type="hidden" name="custom_id" class="form-control" size="36" autocomplete="off" required />
							</div>
							<div class="col-md-4" style="margin-top: 30px;">
								<label class="" for=""></label>
								<a id="button_check_club" style="cursor: pointer;"><span style="font-size: 14px;">Check Club?</span></a>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-3">
								<label class="" for="priority_text">Priority</label><br>
								<input id="priority_text" type="number" name="priority" class="form-control" autocomplete="off" required />
							</div>
							<div class="col-md-4">
								<label class="" for="icdate">Start Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="icdate" type="text" name="start_date" class="form-control input-datepicker add_date" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" readonly="readonly" required />
								</div>
							</div>
							<div class="col-md-4">
								<label class="" for="iedate">End Date *</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input id="iedate" type="text" name="end_date" class="form-control input-datepicker add_date" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" autocomplete="off" readonly="readonly" required />
								</div>
							</div>
						</div>
					</fieldset>
					<div class="form-group form-actions">
						<div class="col-xs-12 text-right">
							<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
							<button id="btn-save" type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
						</div>
					</div>
				</form>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>