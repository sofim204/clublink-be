<style>
	.text-left {
		text-align: left !important;
	}
</style>

<!-- modal edit -->
<div id="modal_edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<a href="javascript:void(0)" data-dismiss="modal" class="pull-right modal-dismiss"><strong><i class="fa fa-times"></i></strong></a>
				<h2 class="modal-title"><strong>Edit</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<form id="fedit" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered" onsubmit="return false;">
					<fieldset>
						<div class="form-group">
							<div class="col-md-6">
								<label class="" for="ieid">ID</label>
								<input id="ieid" type="number" name="id" class="form-control" disabled>
							</div>
							<div class="col-md-6">
								<label class="" for="ieproduct">Product</label>
								<input id="ieproduct" type="text" name="product" class="form-control" disabled>
							</div>
						</div>
						<div class="form-group">
				  		  <div class="col-md-12">
			                <label class="" for="ietext" >Message</label>
							<textarea id="ietext" name="text" class="form-control"></textarea>
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
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="bdetail_post" href="#" class="btn btn-info text-left"><i class="fa fa-info-circle"></i> Detail</a>
						<a id="btakedown_post" href="#" class="btn btn-danger text-left"><i class="fa fa-times-circle"></i> Takedown</a>
						<!-- <button id="active" type="button" class="btn btn-success text-left"><i class="fa fa-file-o"></i> Active</button> -->
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

<!-- modal option participant -->
<div id="modal_option_participant" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
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
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="adetail" href="#" class="btn btn-info text-left"><i class="fa fa-info-circle"></i> Profile Participant</a>
						<a id="acheck_participant" href="#" class="btn btn-info text-left"><i class="fa fa-info-circle"></i> Check Participant</a>
						<!-- <button id="active" type="button" class="btn btn-success text-left"><i class="fa fa-file-o"></i> Active</button> -->
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
<!-- END modal option paticipant -->

<!-- modal detail post -->
<div id="modal_detail_post" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
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
									<!-- <center><h4><b>Detail Postingan</b></h4></center> -->
                                    <!-- <center>
                                        <table>
                                            <tr>
                                                <td>Desc</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivdesc_post"><b></b></td>
                                            </tr>
											<tr>
                                                <td>Attachment</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivattach_post"><b></b></td>
                                            </tr>
                                        </table>
										<p id="reject_note"></p>
                                    </center> -->

									<label for="exampleFormControlInput1">Desc</label>
    								<p id="ivdesc_post"></p>
									<label for="exampleFormControlInput1">Attachment</label>
    								<p id="ivattach_post"></p>
                                </div>	

                            </div>
                        </fieldset>
					</form>
				</div>
				<div class="row" style="margin-top: 1em; ">					
					<div class="col-xs-12 btn-group-vertical">
						<button type="button" class="btn btn-default btn-block text-left" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					</div>
				</div>
			</div>
			<!-- END Modal Body -->
		</div>
	</div>
</div>
<!-- END modal detail post -->