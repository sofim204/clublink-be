<style>
	.text-left {
		text-align: left !important;
	}
	.google_mtitle {
    color: rgb(26, 13, 171);
    font-family: arial, sans-serif;
    font-size: 18px;
    font-weight: normal;
    height: auto;
    line-height: 21.6px;
    list-style-type: decimal;
    text-align: left;
    text-decoration: none;
    visibility: visible;
    white-space: nowrap;
    width: auto;
    padding: 0px;
    margin: 0px;
}
.google_slug {
    color: rgb(0, 102, 33);
    font-family: arial, sans-serif;
    font-size: 14px;
    font-style: normal;
    font-weight: normal;
    height: auto;
    line-height: 16px;
    list-style-type: decimal;
    text-align: left;
    visibility: visible;
    white-space: nowrap;
    width: auto;
    padding: 0px;
    margin: 0px;
}
.google_mdescription {
    color: rgb(84, 84, 84);
    font-family: arial, sans-serif;
    font-size: 13px;
    font-weight: normal;
    height: auto;
    line-height: 18.2px;
    list-style-type: decimal;
    text-align: left;
    visibility: visible;
    width: auto;
    word-wrap: break-word;
    padding: 0px;
    margin: 0px;
    bottom: 4px;
}
.google_mkeyword {
    color: rgb(84, 84, 84);
    font-family: arial, sans-serif;
    font-size: 13px;
    font-weight: normal;
    height: auto;
    line-height: 18.2px;
    list-style-type: decimal;
    text-align: left;
    visibility: visible;
    width: auto;
    word-wrap: break-word;
    padding: 0px;
    margin: 0px;
}
.mb-8px {
	margin-bottom: 8px !important;
}
</style>

<!-- ================================================================
||                                                                 ||
||                        OPTION CLICKED                           ||
||                                                                 ||
================================================================= -->
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
						<a id="adetail" href="#" class="btn btn-info text-left mb-8px"><i class="fa fa-info-circle"></i> Detail</a>
						<button id="b_delete_post" type="button" class="btn btn-danger text-left mb-8px"><i class="fa fa-times-circle"></i> Delete Post</button>
						<button id="b_restore_post" type="button" class="btn btn-success text-left mb-8px"><i class="fa fa-times-circle"></i> Restore Post</button>
						<button id="b_change_status_permanent_inactive" type="button" class="btn btn-info text-left"><i class="fa fa-check-circle"></i> Permanently Account Stop</button>
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

<!-- ================================================================
||                                                                 ||
||                        VALIDATION CLICKED                           ||
||                                                                 ||
================================================================= -->
<div id="modal_validation" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Validation</strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
                <div class="row">
                    <form id="fvalidation" action="<?=base_url_admin(); ?>" method="post" enctype="multipart/form-data" class="form-horizontal" onsubmit="return false;">
                        <fieldset>
                            <div class="form-group">
                                <div class="col-md-12">
									<center><h4><b>Data Redeem</b></h4></center>
                                    <center>
                                        <table>
                                            <tr>
                                                <td>Name</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivnama"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Type</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivtype"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Redemption Exchange</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivre_name"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Telp</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivtelp"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Cost SPT</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivcost_spt"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Amount Get</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivamount_get"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Request Date</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivcdate"><b></b></td>
                                            </tr>
                                        </table>
                                        <p id="ivreason_rejected"></p>
                                    </center>
                                </div>								
								<div class="col-md-12" style="margin-top: 1em; ">
								<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
								<center><h4><b>Data User</b></h4></center>
                                    <center>
                                        <table>
                                            <tr>
                                                <td>Name</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivunama"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivuemail"><b></b></td>
                                            </tr>
											<tr>
                                                <td>Reg Date</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivureg_date"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Telp</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivutelp"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Total Recommendation</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivutotal_recruited"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Is Influencer</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivuis_influencer"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>SPT Balance</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivuwallet_balance"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>IP Address</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivuip_address"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Permanent Inactive</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivupermanent_inactive"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Recommender</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivurecommender"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Device ID</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivudevice_id"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Address</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivuaddress"><b></b></td>
                                            </tr>
                                            <tr>
                                                <td>Signup Method</td>
                                                <td>&nbsp; : &nbsp;</td>
                                                <td id="ivusignup_method"><b></b></td>
                                            </tr>
										</table>
									</center>
								</div>
                            </div>
                        </fieldset>
                    </form>
                </div>
				<!-- <div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<a id="adetail" href="#" class="btn btn-info text-left mb-8px"><i class="fa fa-info-circle"></i> Detail</a>
						<button id="b_delete_post" type="button" class="btn btn-danger text-left mb-8px"><i class="fa fa-times-circle"></i> Delete Post</button>
						<button id="b_restore_post" type="button" class="btn btn-success text-left mb-8px"><i class="fa fa-times-circle"></i> Restore Post</button>
						<button id="b_change_status_permanent_inactive" type="button" class="btn btn-info text-left"><i class="fa fa-check-circle"></i> Permanently Account Stop</button>
					</div>
				</div> -->
				<div class="row" style="margin-top: 1em; ">
					<div class="col-md-12" style="border-top: 1px #afafaf dashed;">&nbsp;</div>
                    <center>
						<div class="col-xs-12 btn-group-horizontal">
							<button id="b_approve" type="button" class="btn btn-info text-left mb-8px" style="display: none;"><i class="fa fa-check-circle"></i> Accept</button>
							<button id="b_reject" type="button" class="btn btn-danger text-left mb-8px" style="display: none;"><i class="fa fa-times-circle"></i> Reject</button>
						</div>
                    </center>
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
||                        REJECT OPTION                           ||
||                                                                 ||
================================================================= -->
<div id="modal_reject_option" class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header modal-header-title text-center">
				<h2 class="modal-title"><strong>Choose Reject Reason : </strong></h2>
			</div>
			<!-- END Modal Header -->

			<!-- Modal Body -->
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 btn-group-vertical" style="text-align: left;">
						<!-- <a id="adetail" href="#" class="btn btn-info text-left mb-8px"><i class="fa fa-info-circle"></i> Detail</a> -->
						<button id="b_reject_option_1" type="button" class="btn btn-danger text-left mb-8px"><i class="fa fa-times-circle"></i> Sorry, you can't redeem PULSA as you don't have enough activity on Sellon <br><i>(Maaf, Kamu tidak dapat menukarkan PULSA karena tidak memiliki cukup aktivitas di Sellon)</i></button>
						<button id="b_reject_option_2" type="button" class="btn btn-success text-left mb-8px"><i class="fa fa-times-circle"></i> Sorry, you're not eligible to request PULSA <br><i>(Maaf, Kamu tidak berhak meminta PULSA)</i></button>
						<button id="b_reject_option_3" type="button" class="btn btn-warning text-left mb-8px"><i class="fa fa-times-circle"></i> Sorry, Sellon has a policy that prevents abusers from requesting PULSA redemption <br><i>(Maaf, Sellon memiliki kebijakan yang mencegah penyalahgunaan untuk meminta penukaran PULSA)</i></button>
						<!-- <button id="b_change_status_permanent_inactive" type="button" class="btn btn-info text-left"><i class="fa fa-check-circle"></i> Permanently Account Stop</button> -->
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