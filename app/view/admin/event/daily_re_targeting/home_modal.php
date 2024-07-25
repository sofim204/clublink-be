<style>
	.text-left {
		text-align: left !important;
	}
	.purple-color-menu{
		background: #bb6bd9;
		color: #ffffff;
		border: 1px solid #ab43d2;
	}
	.purple-color-menu:hover{
		background: #ab43d2;
		color: #ffffff;
	}
	.yellow-color-menu{
		background: #f2c94c;
		color: #ffffff;
		border: 1px solid #eab615;
	}
	.yellow-color-menu:hover{
		background: #eab615;
		color: #ffffff;
	}
	.row .btn-group-vertical > button {
		margin-bottom: 5px;
	}
</style>
<!-- modal detail event -->
<div id="modal_detail_event" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title" id="title-modal"><strong></strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">				
					<form id="fpending" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal" onsubmit="return false;">
					    <fieldset>
                            <div class="form-group">
                                <div class="col-md-12">
									<center><h4><b>Event Re-Targeting</b></h4></center>
                                    <center>
                                        <table>
                                            <tr>
                                                <td>Name</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivnama"><b></b></td>
                                            </tr>
											<tr>
                                                <td>Email</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivemail"><b></b></td>
                                            </tr>
											<tr>
                                                <td>Phone</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivtelp"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Date</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivdate"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Day 1</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivday1"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Day 2</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivday2"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Day 3</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivday3"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Day 4</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivday4"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Day 5</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivday5"><b></b></td>
                                            </tr>
                                        </table>
										<p id="reject_note"></p>
                                    </center>
                                </div>	
                            </div>
                        </fieldset>
					</form>
				</div>
				<div class="row check-telp">
					<div id="faq2" class="panel-group">
						<div class="panel panel-default edit-telp">
							<div class="panel-heading">
								<h4 class="panel-title"><i class="fa fa-angle-right"></i> <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#faq2" href="#faq2_q1" aria-expanded="false">Change & Verification Phone Number</a></h4>
							</div>
							<div id="faq2_q1" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
								<div class="panel-body">
									<div class="form-group">
										<center>
											<div class="col-xs-6 btn-group-horizontal">
												<input id="ivutelp" type="text" class="form-control" placeholder="Input New Phone Number" autocomplete="off" required />
											</div>
											<div class="col-xs-5 btn-group-horizontal">
												<button id="b_edit_telp" type="button" class="btn btn-info text-left mb-8px"><i class="fa fa-check-circle"></i> Change & Verification Phone Number</button>
											</div>
										</center>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default verif-telp">
							<div class="panel-heading">
								<h4 class="panel-title"><i class="fa fa-angle-right"></i> <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#faq2" href="#faq2_q2" aria-expanded="false"> Verification Phone Number Only</a></h4>
							</div>
							<div id="faq2_q2" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
								<div class="panel-body">
								<center>
									<div class="col-xs-12 btn-group-horizontal">
										<button id="b_verif_telp" type="button" class="btn btn-primary text-left mb-8px"><i class="fa fa-check-circle"></i> Verification Phone Number Only</button>
									</div>
								</center>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
					<center>
						<div class="col-xs-12 btn-group-horizontal">
							<button id="b_accepted" type="button" class="btn btn-info text-left mb-8px"><i class="fa fa-check-circle"></i> Accepted</button>
							<button id="b_rejected" type="button" class="btn btn-danger text-left mb-8px"><i class="fa fa-times-circle"></i> Rejected</button>
                            <button id="b_finished" type="button" class="btn btn-success text-left mb-8px"><i class="fa fa-check-circle"></i> Finished</button>
						</div>
					</center>
                    <div class="col-md-12" style="border-top: 1px #afafaf;">&nbsp;</div>
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					</div>
				</div>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>

<!-- ================================================================
||                                                                 ||
||                        SUCCESS OPTION                           ||
||                                                                 ||
================================================================= -->
<div id="modal_reject_note" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Reject Reason : </strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<textarea id="ivurejectnote" name="default-textarea" rows="2" class="form-control push-bit" placeholder="Input reject reason..."></textarea>
					<div class="col-xs-12 btn-group-horizontal">
						<center>
							<button id="b_rejected_note" type="button" class="btn btn-danger text-left mb-2px"><i class="fa fa-check-circle"></i> Rejected</button>
						</center>
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







